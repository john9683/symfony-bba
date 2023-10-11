<?php

namespace App\DataFixtures;

use App\Entity\Paragraph;
use App\Entity\Theme;
use App\Import\ThemeContent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture
{
    public function __construct(
        ThemeContent $articleContent
    ) {
        $this->articleContent = $articleContent;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $themesArray = $this->articleContent->getArticleContent();
        $index = 0;

        foreach ($themesArray as  $theme) {
            $paragraphs = $theme['paragraphsArray'];

            $themeObject = new Theme();
            $themeObject
                ->setCode(array_keys($themesArray)[$index++])
                ->setIsDefault($theme['isDefault'])
                ->setTitle($theme['title'])
                ->setSubtitle($theme['subtitle'])
                ->setKeyword($theme['keyword'])
            ;

            $manager->persist($themeObject);
            $manager->flush();

            foreach ( $paragraphs as $item) {
                $paragraph = new Paragraph();
                $paragraph->setText($item);
                $paragraph->setTheme($themeObject);
                $manager->persist($paragraph);
                $manager->flush();
            }
        }
    }
}
