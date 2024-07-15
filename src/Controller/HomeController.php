<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Testimonial;
use App\Entity\Shop\Product;
use App\Entity\Shop\Category;
use App\Entity\Shop\ProductCollection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Settings\HomepageHeroSetting;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function __invoke(EntityManagerInterface $em): Response
    {
        return $this->render('home/home.html.twig', [
            'herosettings' => $em->getRepository(HomepageHeroSetting::class)->find(1),
            'homepageCategories' => $em->getRepository(Category::class)->getLastCategories(6),
            'homepageProducts' => $em->getRepository(Product::class)->getLastProducts(6),
            'homepageCollections' => $em->getRepository(ProductCollection::class)->findBy(['isMegaMenu' => false]),
            'homepagePosts' => $em->getRepository(Post::class)->getLastPosts(3),
            'homepageTestimonials' => $em->getRepository(Testimonial::class)->getRand(3),

            'productsBestSelling' => $em->getRepository(Product::class)->findBy(['isBestSelling' => true]),
            'productsNewArrival' => $em->getRepository(Product::class)->findBy(['isNewArrival' => true]),
            'productsFeatured' => $em->getRepository(Product::class)->findBy(['isFeaturedProduct' => true]),
            'productsSpecialOffer' => $em->getRepository(Product::class)->findBy(['isSpecialOffer' => true])
        ]);
    }
}
