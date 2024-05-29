<?php

namespace App\DataFixtures;

use App\Entity\Size;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\ProductImage;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppShopFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<SubCategory> $subCategories */
        $subCategories = $manager->getRepository(SubCategory::class)->findAll();


        // Create 20 Brands
        /*
        $brands = [];
        for ($i = 0; $i <= 20; ++$i) {
            $brand = new Brand();
            $brand
                ->setName($this->faker()->unique()->word())
                ->setMetaTitle($brand->getName())
                ->setMetaDescription($this->faker()->realText(100))
                ->setIsActive($this->faker()->numberBetween(0, 1))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ;

            $manager->persist($brand);
            $brands[] = $brand;
        }
        */

        // Create 15 Brands
        $brands = [
            [
                'name' => 'Scott Garza',
                'metaTitle' => 'Sit tempor vel cum f',
                'metaDescription' => 'Tempore voluptatem',
                'isActive' => 1
            ],
            [
                'name' => 'Bangladeshi',
                'metaTitle' => 'Bangladeshi',
                'metaDescription' => NULL,
                'isActive' => 1
            ],
            [
                'name' => 'CK',
                'metaTitle' => 'CK',
                'metaDescription' => 'CK',
                'isActive' => 1
            ],
            [
                'name' => 'Samsang',
                'metaTitle' => 'Samsang',
                'metaDescription' => 'Samsang',
                'isActive' => 1
            ],
            [
                'name' => 'Adidas',
                'metaTitle' => 'Adidas',
                'metaDescription' => NULL,
                'isActive' => 1
                
            ],
            [
                'name' => 'Cocacola',
                'metaTitle' => 'Cocacola',
                'metaDescription' => NULL,
                'isActive' => 1
            ],
            [
                'name' => 'Hero',
                'metaTitle' => 'Hero',
                'metaDescription' => NULL,
                'isActive' => 1
            ],
            [
                'name' => 'Walton',
                'metaTitle' => 'Walton',
                'metaDescription' => 'Walton',
                'isActive' => 1
            ],
            [
                'name' => 'Rolex',
                'metaTitle' => 'Rolex',
                'metaDescription' => 'Rolex',
                'isActive' => 1
            ],
            [
                'name' => 'Lemon',
                'metaTitle' => 'Lemon',
                'metaDescription' => 'Lemon',
                'isActive' => 1
            ],
            [
                'name' => 'HP',
                'metaTitle' => 'HP',
                'metaDescription' => NULL,
                'isActive' => 1
            ],
            [
                'name' => 'CHINA',
                'metaTitle' => 'useful',
                'metaDescription' => NULL,
                'isActive' => 1
            ],
            [
                'name' => 'Indian',
                'metaTitle' => 'titel',
                'metaDescription' => NULL,
                'isActive' => 1
            ],
            [
                'name' => 'GUCCI',
                'metaTitle' => 'Ad eius quam placeat',
                'metaDescription' => 'Voluptas possimus d',
                'isActive' => 1
            ],
            [
                'name' => 'Martin Whitehead',
                'metaTitle' => 'Martin Whitehead',
                'metaDescription' => 'Possimus veniam qu',
                'isActive' => 1
            ],
        ];

        foreach($brands as $brand) {   
            $newbrand = new Brand();
            $newbrand
                ->setName($brand['name'])
                ->setMetaTitle($brand['metaTitle'])
                ->setMetaDescription($brand['metaDescription'])
                ->setIsActive($brand['isActive'])
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ;

            $this->setReference($brand['name'], $newbrand);

            $manager->persist($newbrand);
            $brands[] = $brand;
        }

        // Create 30 Colors
        $colors = [
            [
                'name' => 'Red',
                'hex' => '#ff0000',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'Blue',
                'hex' => '#0000ff',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'Green',
                'hex' => '#008000',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'Orange',
                'hex' => '#ffa500',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'White',
                'hex' => '#ffffff',
                'displayInSearch' => 0,
                'isActive' => 1
                
            ],
            [
                'name' => 'Black',
                'hex' => '#000000',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Yellow',
                'hex' => '#ffff00',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'Violet',
                'hex' => '#ee82ee',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'Silver',
                'hex' => '#c0c0c0',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'Grey',
                'hex' => '#808080',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'LightSlateGray',
                'hex' => '#778899',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Maroon',
                'hex' => '#800000',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Brown',
                'hex' => '#a52a2a',
                'displayInSearch' => 1,
                'isActive' => 1
            ],
            [
                'name' => 'DarkBlue',
                'hex' => '#00008b',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Navy blue',
                'hex' => '#0b5394',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'LightGreen',
                'hex' => '#90ee90',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Purple',
                'hex' => '#800080',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'DarkViolet',
                'hex' => '#9400d3',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Gold',
                'hex' => '#ffd700',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'LightYellow',
                'hex' => '#ffffe0',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Khaki',
                'hex' => '#f0e68c',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'MediumPurple',
                'hex' => '#9370db',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Olive',
                'hex' => '#808000',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'DarkCyan',
                'hex' => '#008b8b',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'SkyBlue',
                'hex' => '#87ceeb',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'MediumSlateblue',
                'hex' => '#7b68ee',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'RosyBrown',
                'hex' => '#bc8f8f',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Chocolate',
                'hex' => '#d2691e',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'Peru',
                'hex' => '#cd853f',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'DarkGoldenrod',
                'hex' => '#b8860b',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
        ];

        foreach($colors as $color) {   
            $newcolor = new Color();
            $newcolor
                ->setName($color['name'])
                ->setHex($color['hex'])
                ->setDisplayInSearch($color['displayInSearch'])
                ->setIsActive($color['isActive'])
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ;

            $this->setReference($color['name'], $newcolor);

            $manager->persist($newcolor);
            $colors[] = $color;
        }

        // Create 5 Sizes
        $sizes = [
            [
                'name' => 'S',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'M',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'L',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'XL',
                'displayInSearch' => 0,
                'isActive' => 1
            ],
            [
                'name' => 'XXL',
                'displayInSearch' => 0,
                'isActive' => 1
            ]
        ];

        foreach($sizes as $size) {   
            $newsize = new Size();
            $newsize
                ->setName($size['name'])
                ->setDisplayInSearch($size['displayInSearch'])
                ->setIsActive($size['isActive'])
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ;

            $this->setReference($size['name'], $newsize);

            $manager->persist($newsize);
            $sizes[] = $size;
        }

        // Create 20 Products by User and admin
        $products = [];
        for ($i = 0; $i <= 20; ++$i) {
            $product = new Product();
            //$slug = strtolower($this->slugger->slug($product->getName()));
            $product
                ->setName($this->faker()->unique()->word())
                //->setSlug($slug)
                ->setContent(1 === mt_rand(0, 1) ? $this->faker()->paragraphs(10, true) : null)
                ->setPrice(rand(100, 100000))
                ->setTax(0.2)
                ->setStock(rand(0, 200))
                ->setViews(rand(10, 160))
                ->setMetaTitle($product->getName())
                ->setMetaDescription($this->faker()->realText(100))
                ->setExternallink(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setWebsite(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setEmail(1 === mt_rand(0, 1) ? $this->faker()->email() : null)
                ->setPhone(1 === mt_rand(0, 1) ? $this->faker()->phoneNumber() : null)
                ->setYoutubeurl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setTwitterUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setInstagramUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setFacebookUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setGoogleplusUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                ->setLinkedinUrl(1 === mt_rand(0, 1) ? $this->faker()->url() : null)
                //->setIsOnline($this->faker()->numberBetween(0, 1))
                //->setIsActive($this->faker()->numberBetween(0, 1))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->addSubCategory($this->faker()->randomElement($subCategories))
            ;

            $this->addReference('product-'.$i, $product);

            //$subCategory = $this->getReference('subCategory-'.$this->faker()->numberBetween(1, 8));
            //$product->addSubCategory($subCategory);

            $brand = $this->getReference('brand-'.$this->faker()->numberBetween(1, 15));
            $product->setBrand($brand);

            $manager->persist($product);
            $products[] = $product;
        }

        // Create 20 Products Images
        $productimages = [];
        for ($i = 0; $i <= 20; ++$i) {
            $productimage = (new ProductImage())
                ->setProduct($this->faker()->randomElement($products))
                ->setImageName('664e7be695487075513142.jpg')
                ->setImageSize(70649)
                ->setImageMimeType('image/jpeg')
                ->setImageOriginalName('avatar-1.jpg')
                ->setImageDimensions([256,256])
                ->setPosition(rand(1, 20))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ;

            $manager->persist($productimage);
            $productimages[] = $productimage;
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppAdminTeamUserFixtures::class,
            AppCategoryFixtures::class,
        ];
    }
}
