<?php

namespace Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateFormModel
{
    /**
     * @Assert\Email()
     * @UniqueUser()
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $firstName;

    /**
     * @Assert\Length(min="6", minMessage="Пароль должен быть длиной не менее 6 символов")
     * @var string|null
     */
    public ?string $plainPassword = null;

    /**
     * @Assert\EqualTo(propertyPath="plainPassword", message="Пароли должны совпадать")
     * @var string|null
     */
    public ?string $confirmPassword = null;
}