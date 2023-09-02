<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RedirectService;
use App\Service\ValidateCsrfTokenTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use ValidateCsrfTokenTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var RedirectService
     */
    private RedirectService $redirectService;

    /**
     * @param UserRepository               $userRepository
     * @param UrlGeneratorInterface        $urlGenerator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RedirectService              $redirectService
     */
    public function __construct(
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $passwordEncoder,
        RedirectService $redirectService
    ) {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->redirectService = $redirectService;
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return
            $request->attributes->get('_route') === self::LOGIN_ROUTE
            && $request->isMethod(Request::METHOD_POST);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
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

    /**
     * @param string[]              $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User
     * @throws InvalidCsrfTokenException
     */
    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        if (!$this->validateToken('authenticate', $credentials['csrf_token'])) {
            throw new InvalidCsrfTokenException();
        }

        return $this->userRepository->findOneBy(['email' => $credentials['email']]);
    }

    /**
     * @param string[]      $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        $session = $request->getSession();
        $session->remove('remember_me');

        return $this->redirectService->redirectToRedirectPath('app_dashboard');
    }
}
