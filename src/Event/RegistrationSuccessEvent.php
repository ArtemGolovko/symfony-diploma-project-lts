<?php

namespace App\Event;

use App\Entity\User;

class RegistrationSuccessEvent
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}