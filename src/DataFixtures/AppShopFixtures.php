<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Shop\Size;
use App\Entity\Shop\Brand;
use App\Entity\Shop\Color;
use App\Entity\Shop\Coupon;
use App\Entity\Shop\Feature;
use App\Entity\Shop\Product;
use App\Entity\Shop\Category;
use App\Entity\Shop\CouponType;
use App\Entity\Shop\SubCategory;
use App\Entity\Shop\FeatureValue;
use App\Entity\Shop\ProductImage;
use App\Entity\Shop\AddProductHistory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Settings\HomepageHeroSetting;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppShopFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    /**
     * @var array<int, SubCategory>
     */
    private array $subCategories = [];

    private int $categoryId = 0;

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
     * @var array<int, Feature>
     */
    private array $features = [];

    /**
     * @var array<int, Brand>
     */
    private array $brands = [];

    /**
     * @var array<int, ProductImage>
     */
    private array $productimages = [];

    /**
     * @var array<int, AddProductHistory>
     */
    private array $producthistories = [];

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    private function createBrands(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 40; ++$i) {
            /** @var Brand $brand */
            $brand = (new Brand())
                ->setId($i)
                ->setName(sprintf("Brand %d", $i))
                ->setMetaTitle(sprintf("Brand %d", $i))
                ->setMetaDescription(sprintf("Brand %d", $i))
                ->setIsOnline(true)
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
            $category = (new Category());
            $category
                ->setId($this->categoryId)
                ->setName($this->faker()->unique()->sentence(1, true))
            ;
            $this->categories[] = $category;
            $manager->persist($category);

            ++$this->categoryId;

            for ($l = 1; $l <= 5; ++$l) {
                $sub = (new Category());
                $sub
                    ->setId($this->categoryId)
                    ->setName($this->faker()->unique()->sentence(1, true))
                ;
                $this->categories[] = $sub;
                $manager->persist($sub);

                ++$this->categoryId;
            }
        }
    }

    private function createSubCategories(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            /** @var SubCategory $subCategory */
            $subCategory = (new SubCategory())
                ->setId($i)
                ->setName($this->faker()->unique()->sentence(1, true))
                ->setColor($this->faker()->hexColor())
                ->setCategory($this->categories[$i % count($this->categories)])
            ;
            $this->subCategories[$i] = $subCategory;
            $manager->persist($subCategory);
        }
    }

    private function createCouponType(ObjectManager $manager): void
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

    private function createFeature(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            /** @var Feature $feature */
            $feature = (new Feature())
                ->setName($this->faker()->unique()->sentence(1, true))
            ;
            $this->features[$i] = $feature;
            $manager->persist($feature);
        }
    }

    private function createHomepageHeroSetting(ObjectManager $manager): void
    {
        // Hero Setting
        $homepages = [
            1 => [
                'title' => 'Discover Product',
                'paragraph' => 'Uncover the best products',
                'content' => 'custom',
                'custom_background_name' => 'hero-section.png',
                'custom_block_one_name' => 'hero-block-1.svg',
                'custom_block_two_name' => 'hero-block-2.svg',
                'custom_block_three_name' => 'hero-block-3.svg',
                'show_search_box' => 1,
            ],
        ];

        foreach ($homepages as $key => $value) {
            $homepage = (new HomepageHeroSetting())
                ->setTitle($value['title'])
                ->setParagraph($value['paragraph'])
                ->setContent($value['content'])
                ->setCustomBackgroundName($value['custom_background_name'])
                ->setCustomBlockOneName($value['custom_block_one_name'])
                ->setCustomBlockTwoName($value['custom_block_two_name'])
                ->setCustomBlockThreeName($value['custom_block_three_name'])
                ->setShowSearchBox((bool) $value['show_search_box'])
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $manager->persist($homepage);
        }
    }

    private function createProductImages(ObjectManager $manager): void
    {
        /** @var array<int, Product> $products */
        $products = $manager->getRepository(Product::class)->findAll();

        for ($i = 1; $i <= 20; ++$i) {
            /** @var ProductImage $productimage */
            $productimage = (new ProductImage())
                ->setId($i)
                ->setProduct($this->faker()->randomElement($products))
                ->setImageName('664e7be695487075513142.jpg')
                ->setImageSize(70649)
                ->setImageMimeType('image/jpeg')
                ->setImageOriginalName('avatar-1.jpg')
                ->setImageDimensions([256, 256])
                ->setPosition(rand(1, 2000))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;
            $this->productimages[$i] = $productimage;
            $manager->persist($productimage);
        }
    }

    private function createProductHistory(ObjectManager $manager): void
    {
        /** @var array<int, Product> $products */
        $products = $manager->getRepository(Product::class)->findAll();

        for ($i = 0; $i <= 20; ++$i) {
            $producthistory = (new AddProductHistory())
                ->setId($i)
                ->setQuantity($this->faker()->numberBetween(1, 2))
                ->setProduct($this->faker()->randomElement($products))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;
            $this->producthistories[$i] = $producthistory;
            $manager->persist($producthistory);
        }
    }

    private function createProductColor(ObjectManager $manager): void
    {
        // Create 30 Colors
        $colors = [
            [
                'name' => 'Red',
                'hex' => '#ff0000',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'Blue',
                'hex' => '#0000ff',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'Green',
                'hex' => '#008000',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'Orange',
                'hex' => '#ffa500',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'White',
                'hex' => '#ffffff',
                'displayInSearch' => 0,
                'isOnline' => 1
                
            ],
            [
                'name' => 'Black',
                'hex' => '#000000',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Yellow',
                'hex' => '#ffff00',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'Violet',
                'hex' => '#ee82ee',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'Silver',
                'hex' => '#c0c0c0',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'Grey',
                'hex' => '#808080',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'LightSlateGray',
                'hex' => '#778899',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Maroon',
                'hex' => '#800000',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Brown',
                'hex' => '#a52a2a',
                'displayInSearch' => 1,
                'isOnline' => 1
            ],
            [
                'name' => 'DarkBlue',
                'hex' => '#00008b',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Navy blue',
                'hex' => '#0b5394',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'LightGreen',
                'hex' => '#90ee90',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Purple',
                'hex' => '#800080',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'DarkViolet',
                'hex' => '#9400d3',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Gold',
                'hex' => '#ffd700',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'LightYellow',
                'hex' => '#ffffe0',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Khaki',
                'hex' => '#f0e68c',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'MediumPurple',
                'hex' => '#9370db',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Olive',
                'hex' => '#808000',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'DarkCyan',
                'hex' => '#008b8b',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'SkyBlue',
                'hex' => '#87ceeb',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'MediumSlateblue',
                'hex' => '#7b68ee',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'RosyBrown',
                'hex' => '#bc8f8f',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Chocolate',
                'hex' => '#d2691e',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'Peru',
                'hex' => '#cd853f',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'DarkGoldenrod',
                'hex' => '#b8860b',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
        ];

        foreach($colors as $color) {   
            $newcolor = (new Color())
                ->setName($color['name'])
                ->setHex($color['hex'])
                ->setDisplayInSearch($color['displayInSearch'])
                ->setIsOnline($color['isOnline'])
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $this->setReference($color['name'], $newcolor);

            $manager->persist($newcolor);
            $colors[] = $color;
        }
    }

    private function createProductSize(ObjectManager $manager): void
    {
        // Create 5 Sizes
        $sizes = [
            [
                'name' => 'S',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'M',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'L',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'XL',
                'displayInSearch' => 0,
                'isOnline' => 1
            ],
            [
                'name' => 'XXL',
                'displayInSearch' => 0,
                'isOnline' => 1
            ]
        ];

        foreach($sizes as $size) {   
            $newsize = (new Size())
                ->setName($size['name'])
                ->setDisplayInSearch($size['displayInSearch'])
                ->setIsOnline($size['isOnline'])
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $this->setReference($size['name'], $newsize);

            $manager->persist($newsize);
            $sizes[] = $size;
        }
    }

    /**
     * @param EntityManagerInterface $manager
     */
    public function load(ObjectManager $manager): void
    {
        /** @var array<int, HomepageHeroSetting> $homepages */
        $homepages = $manager->getRepository(HomepageHeroSetting::class)->findAll();

        /** @var array<int, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        $this->createBrands($manager);
        $this->createCategories($manager);
        $this->createSubCategories($manager);
        $this->createCouponType($manager);
        $this->createCoupons($manager);
        $this->createProductColor($manager);
        $this->createProductSize($manager);
        $manager->flush();

        // Create 20 Products by User and admin
        $products = [];
        for ($p = 0; $p <= 20; ++$p) {
            $product = new Product();
            $product
                ->setId($p)
                ->setName($this->faker()->unique()->sentence(5, true))
                ->setSlug($this->slugger->slug($product->getName())->lower())
                ->setContent(1 === mt_rand(0, 1) ? $this->faker()->paragraphs(10, true) : null)
                ->setRef(sprintf("REF_%d", $p))
                ->setPrice(rand(100, 1600))
                ->setSalePrice(rand(100, 1600))
                ->setTax(0.2)
                ->setStock(rand(100, 1600))
                ->setViews(rand(100, 1600))
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
                ->setIsOnline($this->faker()->numberBetween(0, 1))
                ->setIsFeaturedProduct($this->faker()->numberBetween(0, 1))
                ->setIsOnSale($this->faker()->numberBetween(0, 1))
                ->setEnablereviews($this->faker()->numberBetween(0, 1))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->addSubCategory($this->faker()->randomElement($this->subCategories))
                ->addAddedtofavoritesby($this->faker()->randomElement($users))
                ->setIsonhomepageslider($this->faker()->randomElement($homepages))
                ->setBrand($this->brands[($p % count($this->brands)) + 1])
            ;

            shuffle($this->features);

             /** @var Feature $feature */
            foreach (array_slice($this->features, 0, 3) as $feature) {
                $product->addFeature(
                    (new FeatureValue())
                        ->setFeature($feature)
                        ->setValue($feature->getName())
                );
            }

            $this->addReference('product-'.$p, $product);
            $this->createFeature($manager);

            $manager->persist($product);
            $products[] = $product;
        }

        $this->createProductImages($manager);
        $manager->flush();

        // Create 20 Add Products History
        for ($i = 0; $i <= 20; ++$i) {
            $producthistory = (new AddProductHistory())
                ->setId($i)
                ->setQuantity($this->faker()->numberBetween(1, 2))
                ->setProduct($this->faker()->randomElement($products))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;
            $this->producthistories[$i] = $producthistory;
            $manager->persist($producthistory);
        }
        //$this->createProductHistory($manager);

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppUserFixtures::class,
            AppSettingsFixtures::class,
        ];
    }
}
