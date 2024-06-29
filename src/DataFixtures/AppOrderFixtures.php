<?php

namespace App\DataFixtures;

use App\Entity\Shop\Shipping;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppOrderFixtures extends Fixture
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        // Create 6 Shippings
        $shippings = [
            [
                'name' => 'France',
                'shippingCost' => 5
            ],
            [
                'name' => 'Belgique',
                'shippingCost' => 7
            ],
            [
                'name' => 'Espagne',
                'shippingCost' => 15
            ],
            [
                'name' => 'Allemagne',
                'shippingCost' => 20
            ],
            [
                'name' => 'Luxembourg',
                'shippingCost' => 25
            ],
            [
                'name' => 'Pays-Bas',
                'shippingCost' => 30
            ]
        ];

        foreach($shippings as $shipping) {   
            $newshipping = new Shipping();
            $newshipping
                ->setName($shipping['name'])
                ->setShippingCost($shipping['shippingCost'])
            ;

            $this->setReference($shipping['name'], $newshipping);

            $manager->persist($newshipping);
            $shippings[] = $shipping;
        }

        $manager->flush();
    }
}
