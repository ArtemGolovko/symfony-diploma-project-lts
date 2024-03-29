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
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request               $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response|null
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        $attributes = $accessDeniedException->getAttributes();

        if (in_array(UserAnonymousVoter::IS_ANONYMOUS, $attributes)) {
            return $this->createResponse(
                $request,
                'Страница не доступна для авторизованных пользователей.',
                'app_dashboard'
            );
        }

        if (in_array(UserVerifiedVoter::IS_AUTHENTICATED_AND_VERIFIED, $attributes)
            || in_array(UserVerifiedVoter::IS_AUTHENTICATED_FULLY_AND_VERIFIED, $attributes)) {
            return $this->createResponse(
                $request,
                'Страница доступна только для авторизованных пользователей, которые подтвердили свой email.',
                'app_register'
            );
        }

        if (in_array(UserVerifiedVoter::IS_ANONYMOUS_OR_UNVERIFIED, $attributes)) {
            return $this->createResponse(
                $request,
                'Страница не доступна для авторизованных пользователей и тех кто подтвердили свой email.',
                'app_dashboard'
            );
        }

        return null;
    }

    /**
     * @param Request $request
     * @param string  $message
     * @param string  $redirectPath
     *
     * @return Response
     */
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
