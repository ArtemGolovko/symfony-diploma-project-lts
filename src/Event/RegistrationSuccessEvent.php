<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class RegistrationSuccessEvent
{
    private User $user;
    private Request $request;

    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}