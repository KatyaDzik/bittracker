<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends BaseFixture
{
    public static int $count = 1;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::$count; $i++) {
            $user = (new User())
                ->setEmail($this->faker->email)
                ->setName($this->faker->name)
                ->setPassword('$2y$13$9er/DceUlPKT4YDyXxW8fe/b58SFrRHAXbcANQsax99yHnZMGA5Uy') //password
                ->setState('active');

            $manager->persist($user);
        }

        $manager->flush();
    }
}