<?php

namespace App\Controller;

use App\AppEvents;
use App\Entity\User;
use App\Event\RegistrationSuccessEvent;
use App\Form\Model\RegistrationFormModel;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends AbstractController
{
    use TargetPathTrait;
    /**
     * @Route("/login", name="app_login")
     * @IsGranted("IS_ANONYMOUS")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @IsGranted("IS_ANONYMOUS_OR_UNVERIFIED")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guard,
        LoginFormAuthenticator $authenticator,
        EventDispatcherInterface $dispatcher
    ): Response {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationFormModel $data */
            $data = $form->getData();

            $user = new User();
            $user
                ->setName($data->name)
                ->setEmail($data->email)
                ->setPassword($passwordEncoder->encodePassword($user, $data->plainPassword))
            ;

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $event = new RegistrationSuccessEvent($user, $request);
            $dispatcher->dispatch($event, AppEvents::REGISTRATION_SUCCESS);

            return $guard->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'error' => ''
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @Route("/verify-email",  name="app_verify_email")
     */
    public function verifyEmail(VerifyEmailHelperInterface $verifyEmailHelper, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($user->isVerified()) {
            $path = $this->getTargetPath($request->getSession(), 'main')
                ?? $this->generateUrl('app_dashboard');

            return $this->redirect($path);
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', 'Неверный код подтверждения email');
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $em->flush();

        $path = $this->getTargetPath($request->getSession(), 'main')
            ?? $this->generateUrl('app_dashboard');

        $this->addFlash('success', 'Регистрация успешно завершена');

        return $this->redirect($path);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
