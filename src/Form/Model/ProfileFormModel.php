<?php

namespace App\Form\Model;

use App\Entity\User;
use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileFormModel
{
    /**
     * @param User $user
     *
     * @return ProfileFormModel
     */
    public static function fromUser(User $user): ProfileFormModel
    {
        $model = new self();
        $model->name = $user->getName();
        $model->email = $user->getEmail();
        $model->plainPassword = null;

        return $model;
    }

    /**
     * @var string|null
     */
    public ?string $name;

    /**
     * @Assert\Email(message="Email должен иметь формат электронной почты")
     * @UniqueUser(message="Email уже используется", allowYourself=true)
     * @var string|null
     */
    public ?string $email;

    /**
     * @var string|null
     */
    public ?string $plainPassword;
}