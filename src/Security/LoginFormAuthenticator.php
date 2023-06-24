<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\RedirectService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    public const LOGIN_ROUTE = 'app_login';

    private UserRepository $userRepository;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private RedirectService $redirectService;

    public function __construct(
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        RedirectService $redirectService
    ) {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->redirectService = $redirectService;
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function supports(Request $request)
    {
        return
            $request->attributes->get('_route') === self::LOGIN_ROUTE
            && $request->isMethod(Request::METHOD_POST);
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token')
        ];

        $session = $request->getSession();

        $session->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        $session->set(
            'remember_me',
            $request->request->getBoolean('_remember_me')
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $csrfToken = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new InvalidCsrfTokenException();
        }

        return $this->userRepository->findOneBy(['email' => $credentials['email']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $session = $request->getSession();
        $session->remove('remember_me');

        return $this->redirectService->redirectToRedirectPath('app_dashboard');
    }
}
