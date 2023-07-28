<?php

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormModel
{
    /**
     * @Assert\NotBlank(message="Имя не может быть пустым")
     * @var string
     */
    public string $name;

    /**
     * @Assert\NotBlank(message="Email не может быть пустым")
     * @Assert\Email(message="Email должен иметь формат электронной почты")
     * @UniqueUser(message="Вы уже зарегистрированы")
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $plainPassword;
}
