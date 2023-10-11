<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(
            User::class,
            10,
            function (User $user) use ($manager) {
            $user
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_USER'])
                ->setFirstName($this->faker->firstName)
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
                ->setIsVerified(true)
                ->setApiToken(sha1(uniqid('token')))
                ->setSubscriptionLevel('0')
                ->setPlainEmail(null)
            ;
            }
        );
    }
}
