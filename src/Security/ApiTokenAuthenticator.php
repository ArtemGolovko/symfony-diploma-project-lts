<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization')
            && strtolower(explode(" ", $request->headers->get('Authorization'))[0]) === 'bearer';
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getCredentials(Request $request): string
    {
        return explode(" ", $request->headers->get('Authorization'))[1];
    }

    /**
     * @param string                $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User
     */
    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        $user = $this->userRepository->findOneBy(['apiToken' => $credentials]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Invalid api token');
        }

        return $user;
    }

    /**
     * @param string $credentials
     * @param User   $user
     *
     * @return true
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
        ], 401);
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): void {}

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return void
     * @throws \Exception
     */
    public function start(Request $request, AuthenticationException $authException = null): void
    {
        throw new \Exception("Unreachable");
    }

    /**
     * @return false
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
