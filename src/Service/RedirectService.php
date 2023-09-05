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

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @param SessionInterface      $session
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(SessionInterface $session, UrlGeneratorInterface $urlGenerator)
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $defaultRoute
     * @param array  $parameters
     * @param int    $status
     *
     * @return RedirectResponse
     */
    public function redirectToTargetPath(
        string $defaultRoute,
        array $parameters = [],
        int $status = 302
    ): RedirectResponse {
        $path = $this->getTargetPath($this->session, self::PROVIDER_KEY) ?? $this->urlGenerator->generate(
            $defaultRoute,
            $parameters
        );

        return new RedirectResponse($path, $status);
    }

    /**
     * Redirects to redirect path. Fallbacks to target path if redirect path is empty.
     *
     * @param string $defaultRoute
     * @param array  $parameters
     * @param int    $status
     *
     * @return RedirectResponse
     */
    public function redirectToRedirectPath(
        string $defaultRoute,
        array $parameters = [],
        int $status = 302
    ): RedirectResponse {
        $path = $this->getRedirectPath();

        if (!$path) {
            $path = $this->getTargetPath($this->session, self::PROVIDER_KEY) ?? $this->urlGenerator->generate(
                $defaultRoute,
                $parameters
            );
        }

        return new RedirectResponse($path, $status);
    }

    /**
     * @return string|null
     */
    public function getRedirectPath(): ?string
    {
        return $this->session->get(self::REDIRECT_PATH_KEY);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function setRedirectPath(string $path): void
    {
        $this->session->set(self::REDIRECT_PATH_KEY, $path);
    }

    /**
     * @param string $route
     * @param array  $parameters
     *
     * @return void
     */
    public function setRedirectPathToRoute(string $route, array $parameters = []): void
    {
        $this->setRedirectPath($this->urlGenerator->generate($route, $parameters));
    }
}
