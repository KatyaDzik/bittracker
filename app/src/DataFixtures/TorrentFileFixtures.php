<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\TorrentFile;
use App\Entity\User;
use App\Enum\TorrentFileStatusEnum;
use Doctrine\Persistence\ObjectManager;

class TorrentFileFixtures extends BaseFixture
{
    public static int $count = 1;

    public function load(ObjectManager $manager): void
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        $users = $manager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->setMaxResults(self::$count)
            ->getQuery()
            ->getResult();

        for ($i = 0; $i < self::$count; $i++) {
            $torrent = (new TorrentFile())
                ->setTitle($this->faker->words(rand(1, 4), true))
                ->setDescription($this->faker->paragraphs(rand(1, 4), true))
                ->setStatus(TorrentFileStatusEnum::Unverified->name)
                ->setFile('Fly-Me-To-The-Moon-2024-D-AMZN-WEB-DLRip-1-46Gb-52861-84-66bd93eed0fc1.torrent')
                ->setCategory($categories[array_rand($categories)])
                ->setAuthor($users[array_rand($users)]);

            $manager->persist($torrent);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['app_group', 'torrent_files'];
    }
}