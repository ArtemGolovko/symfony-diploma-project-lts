<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVerifiedVoter extends Voter
{
    public const IS_AUTHENTICATED_AND_VERIFIED = 'IS_AUTHENTICATED_AND_VERIFIED';
    public const IS_AUTHENTICATED_FULLY_AND_VERIFIED = 'IS_AUTHENTICATED_FULLY_AND_VERIFIED';
    public const IS_ANONYMOUS_OR_UNVERIFIED = 'IS_ANONYMOUS_OR_UNVERIFIED';

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param        $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [
            self::IS_AUTHENTICATED_AND_VERIFIED,
            self::IS_AUTHENTICATED_FULLY_AND_VERIFIED,
            self::IS_ANONYMOUS_OR_UNVERIFIED
        ]);
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
        /** @var User $user */
        $user = $token->getUser();

        switch ($attribute) {
            case self::IS_ANONYMOUS_OR_UNVERIFIED:
                return $this->security->isGranted("IS_ANONYMOUS") || !$user->isVerified();

            case self::IS_AUTHENTICATED_AND_VERIFIED:
                return $this->security->isGranted("IS_AUTHENTICATED_REMEMBERED") && $user->isVerified();

            case self::IS_AUTHENTICATED_FULLY_AND_VERIFIED:
                return $this->security->isGranted("IS_AUTHENTICATED_FULLY") && $user->isVerified();
        }

        return false;
    }
}
