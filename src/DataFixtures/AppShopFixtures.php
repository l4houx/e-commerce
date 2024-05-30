<?php

namespace App\DataFixtures;

use App\Entity\Size;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\CouponType;
use App\Entity\SubCategory;
use App\Entity\ProductImage;
use App\Entity\HomepageHeroSetting;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppShopFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    /**
     * @var array<int, Brand>
     */
    private array $brands = [];

    /**
     * @var array<int, Category>
     */
    private array $categories = [];

    /**
     * @var array<int, CouponType>
     */
    private array $couponTypes = [];

    /**
     * @var array<int, Coupon>
     */
    private array $coupons = [];

    /**
     * @var array<int, SubCategory>
     */
    private array $subCategories = [];

    private int $categoryId = 0;

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    private function createBrands(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 15; ++$i) {
            /** @var Brand $brand */
            $brand = (new Brand())
                ->setId($i)
                ->setName(sprintf("Brand %d", $i))
                ->setMetaTitle(sprintf("Brand %d", $i))
                ->setMetaDescription(sprintf("Brand %d", $i))
                ->setIsActive(1)
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;
            $this->brands[$i] = $brand;
            $manager->persist($brand);
        }
    }

    private function createCategories(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $manager->persist($category = (new Category())
                ->setId($this->categoryId)
                ->setName(sprintf("Categorie %d", $this->categoryId)))
            ;

            $this->categoryId++;

            for ($l = 1; $l <= 5; ++$l) {
                $manager->persist($subCategory = (new Category())
                    ->setId($this->categoryId)
                    ->setName(sprintf("Categorie %d", $this->categoryId)))
                ;

                $this->categories[] = $subCategory;
                $this->categoryId++;
            }
        }
    }

    private function createSubCategories(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            /** @var SubCategory $subCategory */
            $subCategory = (new SubCategory())
                ->setId($i)
                ->setName(sprintf("SubCategoies %d", $i))
                ->setColor($this->faker()->hexColor())
                ->setCategory($this->categories[$i % count($this->categories)])
            ;
            $this->subCategories[$i] = $subCategory;
            $manager->persist($subCategory);
        }
    }

    private function createcCouponType(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            /** @var CouponType $couponType */
            $couponType = (new CouponType())
                ->setId($i)
                ->setName($this->faker()->unique()->word())
            ;
            $this->couponTypes[$i] = $couponType;
            $manager->persist($couponType);
        }
    }

    private function createCoupons(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            /** @var Coupon $coupon */
            $coupon = (new Coupon())
                ->setId($i)
                ->setCode($this->faker()->regexify('[A-Z]{5}[0-4]{3}'))
                ->setContent($this->faker()->realText(100))
                ->setDiscount(rand(10, 160))
                ->setMaxUsage(rand(1, 2))
                ->setCouponType($this->couponTypes[($i % count($this->couponTypes)) + 1])
                ->setValid(true)
                //->addOrder()
                ->setValidity(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-20 days', '+10 days')))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;
            $this->coupons[$i] = $coupon;
            $manager->persist($coupon);
        }
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<HomepageHeroSetting> $homepages */
        $homepages = $manager->getRepository(HomepageHeroSetting::class)->findAll();

        $this->createBrands($manager);
        $this->createCategories($manager);
        $this->createSubCategories($manager);
        $this->createcCouponType($manager);
        $this->createCoupons($manager);
        $manager->flush();

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
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
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
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $this->setReference($size['name'], $newsize);

            $manager->persist($newsize);
            $sizes[] = $size;
        }

        // Create 20 Products by User and admin
        $products = [];
        for ($i = 0; $i <= 20; ++$i) {
            $product = new Product();
            // $slug = strtolower($this->slugger->slug($product->getName()));
            $product
                ->setName($this->faker()->unique()->word())
                // ->setSlug($slug)
                ->setContent(1 === mt_rand(0, 1) ? $this->faker()->paragraphs(10, true) : null)
                ->setPrice($this->faker()->randomFloat(2))
                ->setSalePrice($this->faker()->randomFloat(2))
                ->setDiscount($this->faker()->randomFloat(2))
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
                // ->setIsOnline($this->faker()->numberBetween(0, 1))
                // ->setIsActive($this->faker()->numberBetween(0, 1))
                ->setIsFeaturedProduct($this->faker()->numberBetween(0, 1))
                ->setIsBestSelling($this->faker()->numberBetween(0, 1))
                ->setIsNewArrival($this->faker()->numberBetween(0, 1))
                ->setIsOnSale($this->faker()->numberBetween(0, 1))
                ->setEnablereviews($this->faker()->numberBetween(0, 1))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->addSubCategory($this->faker()->randomElement($this->subCategories))
                ->addAddedtofavoritesby($this->getReference('user-'.$this->faker()->numberBetween(1, 10)))
                ->setIsonhomepageslider($this->faker()->randomElement($homepages))
                ->setBrand($this->brands[($i % count($this->brands)) + 1])
            ;

            $this->addReference('product-'.$i, $product);

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
                ->setImageDimensions([256, 256])
                ->setPosition(rand(1, 20))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
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
            AppSettingsFixtures::class,
            AppCategoryFixtures::class,
        ];
    }
}
