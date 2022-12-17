<?php

namespace App\Security;

use App\Security\Voter\UserAnonymousVoter;
use App\Security\Voter\UserVerifiedVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $attributes = $accessDeniedException->getAttributes();

        if (in_array(UserAnonymousVoter::IS_ANONYMOUS, $attributes)) {
            return $this->createResponse(
                $request,
                'Страница не доступна для авторизованых пользователей.',
                'app_dashboard'
            );
        }

        if (in_array(UserVerifiedVoter::IS_AUTHENTICATED_AND_VERIFIED, $attributes)
            || in_array(UserVerifiedVoter::IS_AUTHENTICATED_FULLY_AND_VERIFIED, $attributes)) {
            return $this->createResponse(
                $request,
                'Страница доступна только для авторизованых пользователей, которые подвердили свой email.',
                'app_register'
            );
        }

        if (in_array(UserVerifiedVoter::IS_ANONYMOUS_OR_UNVERIFIED, $attributes)) {
            return $this->createResponse(
                $request,
            'Страница не доступна для авторизованых пользователей и тех кто подвердили свой email.',
            'app_dashboard'
            );
        }

        return null;
    }

    public function createResponse(Request $request, string $message, string $redirectPath): Response
    {
        $request
            ->getSession()
            ->getFlashBag()
            ->add('error', $message)
        ;

        return new RedirectResponse($this->urlGenerator->generate($redirectPath));
    }
}