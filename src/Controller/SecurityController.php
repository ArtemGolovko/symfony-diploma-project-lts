<?php

namespace App\Controller;

use App\AppEvents;
use App\Event\RegistrationSuccessEvent;
use App\Form\Model\RegistrationFormModel;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\Redirect;
use App\Service\UserService;
use App\Service\Verification\Exception\UserAlreadyVerifiedException;
use App\Service\Verification\VerifyEmail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @IsGranted("IS_ANONYMOUS")
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @IsGranted("IS_ANONYMOUS_OR_UNVERIFIED")
     *
     * @param Request                   $request
     * @param UserService               $userService
     * @param GuardAuthenticatorHandler $guard
     * @param LoginFormAuthenticator    $authenticator
     * @param EventDispatcherInterface  $dispatcher
     *
     * @return Response
     */
    public function register(
        Request $request,
        UserService $userService,
        GuardAuthenticatorHandler $guard,
        LoginFormAuthenticator $authenticator,
        EventDispatcherInterface $dispatcher
    ): Response {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationFormModel $data */
            $data = $form->getData();

            $user = $userService->create($data->email, $data->name, $data->plainPassword);

            $event = new RegistrationSuccessEvent($user, $request);
            $dispatcher->dispatch($event, AppEvents::REGISTRATION_SUCCESS);

            return $guard->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'error' => '',
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     * @Route("/verify-email",  name="app_verify_email")
     * @param VerifyEmail $verifyEmail
     * @param Request     $request
     * @param Redirect    $redirectService
     *
     * @return Response
     */
    public function verifyEmail(
        VerifyEmail $verifyEmail,
        Request $request,
        Redirect $redirectService
    ): Response {
        try {
            $verifyEmail->verifyEmail($request);
        } catch (UserAlreadyVerifiedException $exception) {
            return $redirectService->redirectToTargetPath('app_dashboard');
        } catch (ExpiredSignatureException $exception) {
            $verifyEmail->requestVerification();

            $this->addFlash('error', 'Срок действия кода подтверждения вычерпан.');
            $this->addFlash('success', 'Вам прислан новый код подтверждения.');

            return $this->redirectToRoute('app_register');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', 'Неверный код подтверждения email');

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Регистрация успешно завершена');

        return $redirectService->redirectToTargetPath('app_dashboard');
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \LogicException
     */
    public function logout()
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
