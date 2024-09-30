<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;

class AppFixtures extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        UserFixtures::$count = 10;
        TorrentFileFixtures::$count = 5;

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            AdminUserFixtures::class,
            CategoryFixtures::class,
            UserFixtures::class,
            TorrentFileFixtures::class,
        ];
    }
}
