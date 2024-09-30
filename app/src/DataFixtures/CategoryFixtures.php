<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Movies',
            'Series',
            'Training courses',
            'Books',
            'Games',
            'Software',
            'Music',
        ];

        foreach ($categories as $category) {
            $manager->persist((new Category())->setName($category));
        }

        $manager->flush();
    }
}