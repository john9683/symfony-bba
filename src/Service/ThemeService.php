<?php

namespace App\Service;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;

class ThemeService
{
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getThemesArray(): array
    {
        $themehesArray = [];
        $allThemes = $this->em->getRepository(Theme::class)->findAll();

        foreach ($allThemes as $theme) {
            $themehesArray[] = $theme->getCode();
        }

        return  $themehesArray;
    }
}