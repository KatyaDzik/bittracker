<?php

namespace App\DataFixtures;

use App\Entity\AdminUser;
use Doctrine\Persistence\ObjectManager;

class AdminUserFixtures extends BaseFixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';

    public function load(ObjectManager $manager): void
    {
        $userAdmin = (new AdminUser())
            ->setEmail('admin@admin.com')
            ->setName('Adminovich')
            ->setPassword('$2y$13$rvbq1BmICs0FVeOqGkIYTOUXVr/I1tTHN.m16hngbbkg8HmyQ3KUq') //'password' hash
        ;

        $manager->persist($userAdmin);
        $manager->flush();
        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
    }
}