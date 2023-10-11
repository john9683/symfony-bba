<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

abstract class BaseFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    protected \Faker\Generator $faker;

    /**
     * @var ObjectManager
     */
    protected ObjectManager $manager;

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        $this->manager = $manager;
        $this->loadData($manager);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    abstract function loadData(ObjectManager $manager): void;

    /**
     * @param string $className
     * @param callable $factory
     * @return object
     */
    protected function create(string $className, callable $factory): object
    {
        $entity = new $className();
        $factory($entity);
        $this->manager->persist($entity);

        return $entity;
    }

    /**
     * @param string $className
     * @param int $count
     * @param callable $factory
     * @return void
     */
    protected function createMany(string $className, int $count, callable $factory): void
    {
        for($i=0; $i<$count; $i++) {
            $entity = $this->create($className, $factory);
            $this->addReference("$className|$i", $entity);
        }
        $this->manager->flush();
    }
}
