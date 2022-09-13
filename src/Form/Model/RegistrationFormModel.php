<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormModel
{
    /**
     * @Assert\NotBlank(message="Имя не может быть пустым")
     */
    public string $name;

    /**
     * @Assert\NotBlank(message="Email не может быть пустым")
     * @Assert\Email(message="Email должен иметь формат электронной почты")
     */
    public string $email;

    public string $plainPassword;
}
