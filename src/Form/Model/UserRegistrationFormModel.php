<?php

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormModel
{
     /**
     * @Assert\Email()
     * @Assert\NotBlank(message="Укажите email")
     * @UniqueUser()
     * @var string
     */
    public string $email;

    /**
     * @Assert\NotBlank(message="Укажите ваше имя")
     * @var string
     */
    public string $firstName;

    /**
     * @Assert\NotBlank(message="Укажите пароль")
     * @Assert\Length(min="6", minMessage="Пароль должен быть длиной не менее 6 символов")
     * @var string|null
     */
    public ?string $plainPassword = null;

    /**
     * @Assert\NotBlank(message="Повторите пароль")
     * @Assert\EqualTo(propertyPath="plainPassword", message="Пароли должны совпадать")
     * @var string|null
     */
    public ?string $confirmPassword = null;
}