<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\SubCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        /*
        // Category
        $categories = [
            [
                'name' => 'Seconde vie',
            ],
            [
                'name' => 'Téléphonie & Auto',
            ],
            [
                'name' => 'Image & Son',
            ],
            [
                'name' => 'Informatique',
            ],
            [
                'name' => 'Jeux & loisirs',
            ],
            [
                'name' => 'Objets connectés',
            ],
            [
                'name' => 'Consommables',
            ],
            [
                'name' => 'Connectique',
            ],
        ];

        foreach ($categories as $category) {
            $newcategory = new Category();
            $newcategory->setName($category['name']);
            // $newcategory->addSubCategory($this->faker()->randomElement($subCategories));

            // $slug = strtolower($this->slugger->slug($newcategory->getName()));
            // $newcategory->setSlug($slug);

            $this->setReference($category['name'], $newcategory);

            $manager->persist($newcategory);
        }
        */

        /*
        // Sub Category
        $subCategories = [
            [
                'name' => 'Reconditionnés',
                'color' => '#3f7fca',
            ],
            [
                'name' => 'Occasions',
                'color' => '#3f7fca',
            ],
            [
                'name' => 'Déclassés',
                'color' => '#3f7fca',
            ],
            [
                'name' => 'Téléphonie portable',
                'color' => '#1e81b0',
            ],
            [
                'name' => 'Téléphonie fixe & VoIP',
                'color' => '#1e81b0',
            ],
            [
                'name' => 'Auto',
                'color' => '#1e81b0',
            ],
            [
                'name' => 'Talkie walkie',
                'color' => '#1e81b0',
            ],
            [
                'name' => 'Télévision',
                'color' => '#9141ac',
            ],
            [
                'name' => 'Appareil photo',
                'color' => '#9141ac',
            ],
            [
                'name' => 'Projection',
                'color' => '#9141ac',
            ],
            [
                'name' => 'Home cinéma & Hifi',
                'color' => '#9141ac',
            ],
            [
                'name' => 'Pièces informatique',
                'color' => '#063970',
            ],
            [
                'name' => 'Périphériques',
                'color' => '#063970',
            ],
            [
                'name' => 'Ordinateur portable',
                'color' => '#063970',
            ],
            [
                'name' => 'Ordinateur de bureau',
                'color' => '#063970',
            ],
            [
                'name' => 'Console',
                'color' => '#eb230d',
            ],
            [
                'name' => 'Jeux vidéo',
                'color' => '#eb230d',
            ],
            [
                'name' => 'Accessoires consoles et PC',
                'color' => '#eb230d',
            ],
            [
                'name' => 'Gaming VR',
                'color' => '#eb230d',
            ],
            [
                'name' => 'Maison connectée',
                'color' => '#e07b39',
            ],
            [
                'name' => 'Sécurité connectée',
                'color' => '#e07b39',
            ],
            [
                'name' => 'Sport connecté',
                'color' => '#e07b39',
            ],
            [
                'name' => 'Santé connectée',
                'color' => '#e07b39',
            ],
            [
                'name' => 'Consommable imprimante',
                'color' => '#78e08f',
            ],
            [
                'name' => 'CD / DVD / Blu-ray',
                'color' => '#78e08f',
            ],
            [
                'name' => 'Papeterie',
                'color' => '#78e08f',
            ],
            [
                'name' => 'Pile, batterie',
                'color' => '#78e08f',
            ],
            [
                'name' => 'Connectique PC',
                'color' => '#eb2c79',
            ],
            [
                'name' => 'Connectique Réseau',
                'color' => '#eb2c79',
            ],
            [
                'name' => 'Connectique Secteur',
                'color' => '#eb2c79',
            ],
            [
                'name' => 'Connectique Hifi',
                'color' => '#eb2c79',
            ],
        ];

        foreach($subCategories as $subcategory){
            $newsubcategory = (new SubCategory())
                ->setName($subcategory['name'])
                ->setColor($subcategory['color'])
                ->setCategory()
            ;

            $this->setReference($subcategory['name'], $newsubcategory);

            $manager->persist($newsubcategory);
        }
        */

        $manager->flush();
    }
}
