<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

abstract class BaseFixture extends Fixture implements FixtureGroupInterface
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public static function getGroups(): array
    {
        return ['app_group'];
    }
}