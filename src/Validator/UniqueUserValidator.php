<?php

namespace App\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param UserRepository $userRepository
     * @param Security       $security
     */
    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @param string|null $value
     * @param Constraint  $constraint
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint \App\Validator\UniqueUser */

        if (null === $value || '' === $value) {
            return;
        }

        if ($constraint->allowYourself) {
            /** @var User $user */
            $user = $this->security->getUser();
            if ($user && $user->getEmail() === $value) {
                return;
            }
        }

        if (!$this->userRepository->findOneBy(['email' => $value])) {
            return;
        }

        $this->context->buildViolation($constraint->message)
                      ->setParameter('{{ value }}', $value)
                      ->addViolation()
        ;
    }
}
