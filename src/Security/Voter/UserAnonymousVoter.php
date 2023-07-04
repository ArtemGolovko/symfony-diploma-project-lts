<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAnonymousVoter extends Voter
{
    public const IS_ANONYMOUS = 'IS_ANONYMOUS';

    /**
     * @param string $attribute
     * @param        $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return $attribute === self::IS_ANONYMOUS;
    }

    /**
     * @param string         $attribute
     * @param                $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return !$user instanceof UserInterface;
    }
}
