<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\SubCategory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppCategoryFixtures extends Fixture
{
    use FakerTrait;

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Category
        $categories = [
            [
                'name' => 'Electronic',
            ],
            [
                'name' => 'Home Appliance',
            ],
            [
                'name' => 'Men\'s Fashion',
            ],
            [
                'name' => 'Women\'s Fashion',
            ],
            [
                'name' => 'Furniture',
            ],
            [
                'name' => 'Toy\'s',
            ],
            [
                'name' => 'Gaming',
            ],
            [
                'name' => 'Health',
            ],
            [
                'name' => 'Jewellery',
            ],
            [
                'name' => 'Digital Products',
            ],
            [
                'name' => 'Gym items',
            ],
            [
                'name' => 'Shoes',
            ],
            [
                'name' => 'Accessories & Parts',
            ],
            [
                'name' => 'Camera & Photo',
            ],
            [
                'name' => 'Smart Electronics',
            ],
            [
                'name' => 'Home Audio & Video',
            ],
            [
                'name' => 'Video Games',
            ],
            [
                'name' => 'Portable Audio & Video',
            ],
            [
                'name' => 'Cable & Adapters',
            ],
            [
                'name' => 'Electronic Cigarettes',
            ],
            [
                'name' => 'Batteries',
            ],
            [
                'name' => 'Chargers',
            ],
            [
                'name' => 'Televisions',
            ],
            [
                'name' => 'TV Receivers',
            ],
            [
                'name' => 'Projectors',
            ],
            [
                'name' => 'Audio Amplifier Boards',
            ],
            [
                'name' => 'Digital Cameras',
            ],
            [
                'name' => 'Camcoders',
            ],
            [
                'name' => 'Camera Drones',
            ],
            [
                'name' => 'Action Cameras',
            ],
            [
                'name' => 'Wearable Devices',
            ],
            [
                'name' => 'Smart Home Applience',
            ],
            [
                'name' => 'Smart Wearable Accessories',
            ],
            [
                'name' => 'Smart Remote Controls',
            ],
            [
                'name' => 'Game Consoles',
            ],
            [
                'name' => 'Handheld Game Players',
            ],
            [
                'name' => 'Game Controllers',
            ],
            [
                'name' => 'Console Chargers',
            ],
            [
                'name' => 'Earphones & Headphones',
            ],
            [
                'name' => 'Speakers',
            ],
            [
                'name' => 'MP3 Players',
            ],
            [
                'name' => 'Microphones',
            ],
            [
                'name' => 'Art',
            ],
            [
                'name' => 'Celebrations',
            ],
            [
                'name' => 'Kitchen',
            ],
            [
                'name' => 'Home Storage',
            ],
            [
                'name' => 'Home Decor',
            ],
            [
                'name' => 'Garden Supplies',
            ],
            [
                'name' => 'Fabric & Swing Supplies',
            ],
            [
                'name' => 'Needle Arts & Crafts',
            ],
            [
                'name' => 'Scrapbooking & Stamps',
            ],
            [
                'name' => 'Kitchen Event & Parties',
            ],
            [
                'name' => 'Baloons',
            ],
            [
                'name' => 'Artificial & Dried Flowers',
            ],
            [
                'name' => 'Gift Bags & Boxes',
            ],
            [
                'name' => '5D DIY Diamond Painting',
            ],
            [
                'name' => 'Bakeware',
            ],
            [
                'name' => 'Drinkware',
            ],
            [
                'name' => 'Kitchen Tools & Gadgets',
            ],
            [
                'name' => 'Event & Parties',
            ],
            [
                'name' => 'Painting & Calligraphy',
            ],
            [
                'name' => 'Wall Stickers',
            ],
            [
                'name' => 'Figurines & Miniatures',
            ],
            [
                'name' => 'Wall Clocks',
            ],
            [
                'name' => 'Storage Boxes & Bins',
            ],
            [
                'name' => 'Laundry Baskets',
            ],
            [
                'name' => 'Makeup Organizers',
            ],
        ];

        foreach($categories as $category) {   
            $newcategory = new Category();
            $newcategory->setName($category['name']);

            //$slug = strtolower($this->slugger->slug($newcategory->getName()));
            //$newcategory->setSlug($slug);

            $this->setReference($category['name'], $newcategory);

            $manager->persist($newcategory);
        }

        // Sub Category
        $subCategories = [
            [
                'name' => 'France',
                'parent' => null
            ],
            [
                'name' => 'Monde',
                'parent' => null
            ],
            [
                'name' => 'Politique',
                'parent' => 'France'
            ],
            [
                'name' => 'Associations',
                'parent' => 'France'
            ],
            [
                'name' => 'Economie',
                'parent' => 'Monde'
            ]
        ];

        foreach($subCategories as $subcategory){   
            $newsubcategory = (new SubCategory())
                ->setName($subcategory['name'])
                ->setCategory($this->faker()->randomElement($categories))
            ;

            $this->setReference($subcategory['name'], $newsubcategory);

            $manager->persist($newsubcategory);
        }

        $manager->flush();
    }
}
