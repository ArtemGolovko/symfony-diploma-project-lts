<?php

namespace App\Service;

use Doctrine\Common\Annotations\Annotation\Required;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait ValidateCsrfTokenTrait
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private CsrfTokenManagerInterface $tokenManager;

    /**
     * @Required
     *
     * @param CsrfTokenManagerInterface $tokenManager
     *
     * @return void
     */
    public function setTokenManager(CsrfTokenManagerInterface $tokenManager): void
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param string $id
     * @param string $token
     *
     * @return bool
     */
    public function validateToken(string $id, string $token): bool
    {
        $token = new CsrfToken($id, $token);

        return $this->tokenManager->isTokenValid($token);
    }
}
