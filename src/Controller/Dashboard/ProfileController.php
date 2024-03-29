<?php

namespace App\Controller\Dashboard;

use App\Entity\Module;
use App\Entity\User;
use App\Entity\ValueObject\Subscription;
use App\Form\CreateModuleFormType;
use App\Form\Model\ProfileFormModel;
use App\Form\ProfileFormType;
use App\Helper\ValidateCsrfTokenTrait;
use App\Service\ArticleService;
use App\Service\ModuleService;
use App\Service\SubscriptionService;
use App\Service\UserService;
use App\Service\Verification\Exception\NewEmailAlreadyVerifiedException;
use App\Service\Verification\VerifyNewEmail;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * @IsGranted("IS_AUTHENTICATED_AND_VERIFIED")
 */
class ProfileController extends AbstractController
{
    use ValidateCsrfTokenTrait;

    /**
     * @Route("/dashboard", name="app_dashboard")
     * @param FlashBagInterface   $flashBag
     * @param SubscriptionService $subscriptionService
     * @param ArticleService      $articleService
     *
     * @return Response
     */
    public function dashboard(
        FlashBagInterface $flashBag,
        SubscriptionService $subscriptionService,
        ArticleService $articleService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $diffInDays = $subscriptionService->expiresInDays($this->getUser()->getSubscription());
        $statistics = $articleService->getStatisticsForUser($user);
        $article = $articleService->getLatestForUser($user);

        if ($diffInDays && $diffInDays < 3) {
            $flashBag->add(
                'warning',
                sprintf("Подписка истекает через %d %s", $diffInDays, ($diffInDays === 1) ? "день" : "дня")
            );
        }

        return $this->render('dashboard/profile/dashboard.html.twig', [
            'monthCount' => $statistics['monthsCount'],
            'totalCount' => $statistics['totalCount'],
            'article' => $article,
        ]);
    }

    /**
     * @Route("/dashboard/subscription", name="app_dashboard_subscription")
     * @return Response
     */
    public function subscription(): Response
    {
        return $this->render('dashboard/profile/subscription.html.twig');
    }

    /**
     * @Route("/dashboard/order-subscription/{level}", name="app_dashboard_request_subscription")
     * @param string              $level
     * @param Request             $request
     * @param SubscriptionService $subscriptionService
     *
     * @return Response
     */
    public function requestSubscription(
        string $level,
        Request $request,
        SubscriptionService $subscriptionService
    ): Response {
        $flashBug = $request->getSession()->getFlashBag();

        if ($this->validateToken('request', $request->query->get('_csrf', ''))) {
            /** @var User $user */
            $user = $this->getUser();
            $subscriptionService->upgrade($user, $level);

            $date = $user->getSubscription()->getExpiresAt()->format('d.m.Y');

            $flashBug->add(
                'success',
                sprintf('Подписка %s оформлена, до %s', mb_convert_case($level, MB_CASE_TITLE), $date)
            );
        }
        else {
            $flashBug->add('error', 'Неверный csrf токен');
        }

        return $this->redirectToRoute('app_dashboard_subscription');
    }

    /**
     * @Route("/dashboard/verify-new-email", name="app_dashboard_verify_new_email")
     * @param Request        $request
     * @param VerifyNewEmail $verifyNewEmail
     *
     * @return Response
     */
    public function verifyNewEmail(
        Request $request,
        VerifyNewEmail $verifyNewEmail
    ): Response {
        $flashBag = $request->getSession()->getFlashBag();

        try {
            $verifyNewEmail->verifyNewEmail($request);
        } catch (NewEmailAlreadyVerifiedException $exception) {
            $flashBag->add('error', 'Ви уже изменили почту');

            return $this->redirectToRoute('app_dashboard_profile');
        } catch (ExpiredSignatureException $exception) {
            $this->addFlash('error', 'Срок действия кода подтверждения вычерпан.');

            return $this->redirectToRoute('app_dashboard_profile');
        } catch (VerifyEmailExceptionInterface $exception) {
            $flashBag->add('error', 'Не правильный код подтверждения');

            return $this->redirectToRoute('app_dashboard_profile');
        }

        $flashBag->add('success', 'Email изменен');

        return $this->redirectToRoute('app_dashboard_profile');
    }

    /**
     * @Route("/dashboard/profile", name="app_dashboard_profile")
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param VerifyNewEmail               $verifyNewEmail
     *
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function profile(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        VerifyNewEmail $verifyNewEmail
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, ProfileFormModel::fromUser($user));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ProfileFormModel $data */
            $data = $form->getData();
            $flashBag = $request->getSession()->getFlashBag();
            $changed = false;

            if ($data->name && $data->name !== $user->getName()) {
                $user->setName($data->name);
                $changed = true;
            }

            if ($data->plainPassword) {
                $user->setPassword($passwordEncoder->encodePassword($user, $data->plainPassword));
                $changed = true;
            }

            if ($data->email && $user->getEmail() !== $data->email) {
                $verifyNewEmail->requestVerification($data->email);
                $flashBag->add('success', 'Для изменения электронной почты подтвердите новую электронною почту');
            }

            if ($changed) {
                $this->getDoctrine()->getManager()->flush();
                $flashBag->add('success', 'Профиль успешно изменен');
            }

            return $this->redirectToRoute('app_dashboard_profile');
        }

        return $this->render('dashboard/profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/modules", name="app_dashboard_modules")
     * @param Request            $request
     * @param ModuleService      $moduleService
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function modules(Request $request, ModuleService $moduleService, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(CreateModuleFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getSubscription()->getLevel() !== Subscription::PRO) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    'Для добавления модулей необходим уровень подписки PRO.'
                );

                return $this->redirectToRoute('app_dashboard_modules');
            }

            /** @var Module $module */
            $module = $form->getData();
            $module->setAuthor($user);
            $moduleService->save($module);

            $request->getSession()->getFlashBag()->add('success', 'Модуль успешно добавлен');

            return $this->redirectToRoute('app_dashboard_modules');
        }

        $pagination = $paginator->paginate(
            $moduleService->getModulesForUserQuery($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('dashboard/profile/modules.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/dashboard/modules/{id}/delete", name="app_dashboard_module_delete")
     * @param Module        $module
     * @param ModuleService $moduleService
     * @param Request       $request
     *
     * @return Response
     */
    public function deleteModule(
        Module $module,
        ModuleService $moduleService,
        Request $request
    ): Response {
        $flashBag = $request->getSession()->getFlashBag();

        if (!$this->validateToken('delete', $request->query->get('_csrf', ''))) {
            $flashBag->add('error', 'Неверный csrf токен.');

            return $this->redirectToRoute('app_dashboard_modules');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($user->getSubscription()->getLevel() !== Subscription::PRO) {
            $flashBag->add('error', 'Для удаления модулей необходим уровень подписки PRO.');

            return $this->redirectToRoute('app_dashboard_modules');
        }

        if ($module->getAuthor() !== $user) {
            $flashBag->add('error', 'Вы не являетесь автором этого модуля.');

            return $this->redirectToRoute('app_dashboard_modules');
        }

        $moduleService->remove($module);
        $flashBag->add('success', 'Модуль успешно удален.');

        return $this->redirectToRoute('app_dashboard_modules');
    }

    /**
     * @Route("/dashboard/profile/regenerate-api-token", methods="POST",
     *                                                   name="app_dashboard_profile_regenerate_api_token")
     * @param Request     $request
     * @param UserService $userService
     *
     * @return Response
     */
    public function regenerateApiToken(Request $request, UserService $userService): Response
    {
        if (!$this->validateToken('api_token', $request->query->get('_csrf', ''))) {
            return $this->json([
                'error' => 'Неверный csrf токен',
            ], 400);
        }

        /** @var User $user */
        $user = $this->getUser();
        $userService->regenerateApiToken($user);

        return $this->json([
            'token' => $user->getApiToken(),
        ]);
    }
}
