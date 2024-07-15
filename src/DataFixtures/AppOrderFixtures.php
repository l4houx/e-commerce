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
        /*
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
        */

        // Create 330 Add Shipping
        $shippings = [];
        for ($i = 0; $i <= 100; ++$i) {
            $shipping = (new Shipping())
                ->setName($this->faker()->unique()->country())
                ->setShippingCost(rand(10, 100))
            ;
            $manager->persist($shipping);
            $shippings[] = $shipping;
        }

        $manager->flush();
    }
}
