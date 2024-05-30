<?php

namespace App\DataFixtures;

use App\Entity\Size;
use App\Entity\Brand;
use App\Entity\Color;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFeatureFixtures extends Fixture
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {

    }
}
