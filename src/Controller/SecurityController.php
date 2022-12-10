<?php

namespace App\Controller;

use App\AppEvents;
use App\Entity\User;
use App\Event\RegistrationSuccessEvent;
use App\Form\Model\RegistrationFormModel;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    use TargetPathTrait;
    /**
     * @Route("/login", name="app_login")
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
     * @Route("/verify-email/{verificationCode}",  name="app_verify_email")
     */
    public function verifyEmail(
        $verificationCode
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($user->getVerificationCode() === $verificationCode) {
            $user->setVerificationCode(null);

            $em->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->redirectToRoute('app_register');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
