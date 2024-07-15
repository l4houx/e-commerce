<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Shop\Review;
use App\Entity\Testimonial;
use App\Entity\Shop\Product;
use App\Entity\User\Customer;
use App\Entity\Shop\AddressCustoner;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppAddressCustonerFixtures extends Fixture
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        /** @var array<int, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        $names = ['Home', 'Office'];
        $name = $this->faker()->randomElement($names);

        $genres = ['male', 'female'];
        $genre = $this->faker()->randomElement($genres);

        // Create 117 Address Custoner
        for ($i = 0; $i <= 117; ++$i) {
            $address = new AddressCustoner();
            $address
                ->setUser($this->faker()->randomElement($users))
                ->setName($name)
                ->setClientName($this->faker()->lastName . ' ' . $this->faker()->firstName($genre))
                ->setMoreDetails(1 === mt_rand(0, 1) ? $this->faker()->paragraphs(10, true) : null)
                ->setStreet($this->faker()->address)
                ->setStreet2(1 === mt_rand(0, 1) ? $this->faker()->address : null)
                ->setPostalcode($this->faker()->postcode)
                ->setCity($this->faker()->city)
                ->setCountryCode($this->faker()->country)
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $manager->persist($address);
        }

        $manager->flush();
    }
}
