<?php

namespace App\Form\Model;

use App\Entity\User;
use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileFormModel
{
    public static function fromUser(User $user): self
    {
        $model = new self();
        $model->name = $user->getName();
        $model->email = $user->getEmail();
        $model->plainPassword = null;

        return $model;
    }

    public ?string $name;

    /**
     * @Assert\Email(message="Email должен иметь формат электронной почты")
     * @UniqueUser(message="Email уже используется", allowYourself=true)
     */
    public ?string $email;

    public ?string $plainPassword;
}