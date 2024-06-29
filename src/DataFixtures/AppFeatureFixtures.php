<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFeatureFixtures extends Fixture
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {

    }
}
