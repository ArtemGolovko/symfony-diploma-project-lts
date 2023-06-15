<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class RedirectService
{
    use TargetPathTrait;

    private const PROVIDER_KEY = 'main';
    private const REDIRECT_PATH_KEY = 'redirect_path';

    private SessionInterface $session;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(SessionInterface $session, UrlGeneratorInterface $urlGenerator)
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    public function redirectToTargetPath(string $defaultRoute, array $parameters = [], int $status = 302): RedirectResponse
    {
        $path = $this->getTargetPath($this->session, self::PROVIDER_KEY)
            ?? $this->urlGenerator->generate($defaultRoute, $parameters);

        return new RedirectResponse($path, $status);
    }

    public function redirectToRedirectPath(string $defaultRoute, array $parameters = [], int $status = 302): RedirectResponse
    {
        $path = $this->getRedirectPath();
        if (null === $path) {
            $path = $this->getTargetPath($this->session, self::PROVIDER_KEY)
                ?? $this->urlGenerator->generate($defaultRoute, $parameters);
        }

        return new RedirectResponse($path, $status);
    }

    public function getRedirectPath(): ?string {
        return $this->session->get(self::REDIRECT_PATH_KEY);
    }

    public function setRedirectPath(string $path) {
        return $this->session->set(self::REDIRECT_PATH_KEY, $path);
    }

    public function setRedirectPathToRoute(string $route, array $parameters = []) {
        $this->setRedirectPath($this->urlGenerator->generate($route, $parameters));
    }
}