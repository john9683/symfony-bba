<?php

namespace Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ModuleFormModel
{
    /**
     * @Assert\NotBlank(message="Укажите название модуля")
     * @var string
     */
    public string $title;

    /**
     * @Assert\NotBlank(message="Укажите содержание модуля")
     * @var string
     */
    public string $layout;
}