<?php

namespace App\DataFixtures;

use App\Entity\Module;
use App\Import\ModuleLayout;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ModuleFixtures extends Fixture
{
    public function __construct(
        ModuleLayout $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $modulesArray = $this->layout->getModuleLayout();

        foreach ($modulesArray as $module) {
            $moduleObject = new Module();
            $moduleObject
                ->setIsPublic(true)
                ->setLayout($module)
                ->setTitle('Предустановленный модуль')
            ;
            $manager->persist($moduleObject);
        }

        $manager->flush();
    }
}
