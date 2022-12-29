<?php

namespace App\Controller\Dashboard;

use App\Entity\User;
use App\Form\Model\ProfileFormModel;
use App\Form\ProfileFormType;
use App\Service\Mailer\Mailer;
use App\Service\Mailer\Receiver;
use App\Service\SubscriptionService;
use App\Service\UpgradeEmailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @IsGranted("IS_AUTHENTICATED_AND_VERIFIED")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard(SessionInterface $session, SubscriptionService $subscriptionService): Response
    {
        $diffInDays = $subscriptionService->expiresInDays($this->getUser()->getSubscription());

        if ($diffInDays && $diffInDays < 3) {
            $session->getFlashBag()->add(
                'warning',
                sprintf("Подписка истекает через %d %s", $diffInDays, ($diffInDays === 1) ? "день" : "дня")
            );
        }

        return $this->render('dashboard/profile/dashboard.html.twig');
    }

    /**
     * @Route("/dashboard/subscription", name="app_dashboard_subscription")
     */
    public function subscription(): Response
    {
        return $this->render('dashboard/profile/subscription.html.twig');
    }

    /**
     * @Route("/dashboard/order-subscription/{level}", name="app_dashboard_request_subscription")
     */
    public function requestSubscription(
        $level,
        Request $request,
        CsrfTokenManagerInterface $manager,
        SubscriptionService $subscriptionService
    ): Response {
        $csrfToken = new CsrfToken('request', $request->query->get('_csrf', ''));
        $flashBug = $request->getSession()->getFlashBag();

        if ($manager->isTokenValid($csrfToken)) {
            $user = $this->getUser();
            $subscriptionService->upgrade($user, $level);

            $date = $user->getSubscription()->getExpiresAt()->format('d.m.Y');

            $flashBug->add(
                'success',
                sprintf('Подписка %s оформлена, до %s', mb_convert_case($level, MB_CASE_TITLE), $date)
            );
        } else {
            $flashBug->add('error', 'Неверный csrf токен');
        }

        return $this->redirectToRoute('app_dashboard_subscription');
    }

    /**
     * @Route("/dashboard/verify-upgrade-email/{verificationCode}", name="app_dashboard_verify_upgrade_email")
     */
    public function verifyUpgradeEmail(
        string $verificationCode,
        UpgradeEmailService $upgradeEmail,
        SessionInterface $session
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $flashBag = $session->getFlashBag();

        if ($upgradeEmail->upgradeEmail($user, $verificationCode)) {
            $flashBag->add('success', 'Email изменен');
        } else {
            $flashBag->add('error', 'Не правильный код подтверждения');
        }

        return $this->redirectToRoute('app_dashboard_profile');
    }

    /**
     * @Route("/dashboard/profile", name="app_dashboard_profile")
     */
    public function profile(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UpgradeEmailService $upgradeEmail,
        Mailer $mailer
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, ProfileFormModel::fromUser($user));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ProfileFormModel $data */
            $data = $form->getData();
            $flashBag = $request->getSession()->getFlashBag();

            if ($data->name)
                $user->setName($data->name);

            if ($data->plainPassword)
                $user->setPassword($passwordEncoder->encodePassword($user, $data->plainPassword));

            if ($data->email && $data->email !== $user->getEmail()) {
                $verificationCode = $upgradeEmail->requestUpgrade($user, $data->email);
                $mailer->sendUpgradeEmailVerification(new Receiver($user->getName(), $data->email), $verificationCode);

                $flashBag->add('success', 'Для изменения электронной почты подтвердите новую электронною почту');
            }

            $this->getDoctrine()->getManager()->flush();
            $flashBag->add('success', 'Профиль успешно изменен');

            return $this->redirectToRoute('app_dashboard_profile');
        }

        return $this->render('dashboard/profile/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/modules", name="app_dashboard_modules")
     */
    public function modules(): Response
    {
        return $this->render('dashboard/profile/modules.html.twig');
    }
}
