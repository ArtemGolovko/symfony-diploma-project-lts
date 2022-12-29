<?php

namespace App\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserValidator extends ConstraintValidator
{
    private UserRepository $userRepository;
    private Security $security;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
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
            ->addViolation();
    }

}
