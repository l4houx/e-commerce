<?php

namespace App\Controller;

use App\Entity\Shop\Product;
use App\Entity\Traits\HasLimit;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Shop\ProductRepository;
use App\Repository\Shop\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Shop\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PaginatorInterface $paginator,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/shop', name: 'shop', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $products = $this->productRepository->findForPagination($page);

        // $products = $this->productRepository->findAll();
        // $categories = $this->categoryRepository->findAll();

        return $this->render('shop/index.html.twig', compact('products'));
    }

    #[Route(path: '/shop/product/{id}', name: 'shop_product', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function product(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $similarProducts = $this->productRepository->findSimilar($product);
        // $categories = $this->categoryRepository->findAll();

        if (!$product) {
            $this->addFlash('danger', $this->translator->trans('The product not be found'));

            return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
        }

        $product->viewed();
        $em->persist($product);
        $em->flush();

        return $this->render('shop/product.html.twig', compact('product', 'similarProducts'));
    }

    #[Route(path: '/shop/product/subcategory/{id}/filter', name: 'shop_subcategory_filter', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function subCategoryFilter(Request $request, SubCategoryRepository $subCategoryRepository, $id): Response
    {
        $products = $this->paginator->paginate(
            $subCategoryRepository->find($id)->getProducts(),
            $request->query->getInt('page', 1),
            HasLimit::PRODUCT_LIMIT,
            ['wrap-queries' => true]
        );

        // $products = $subCategoryRepository->find($id)->getProducts();
        $subCategory = $subCategoryRepository->find($id);
        // $categories = $this->categoryRepository->findAll();

        return $this->render('shop/filter.html.twig', compact('products', 'subCategory'));
    }

    #[Route(path: '/shop/product/subcategory/{id}', name: 'shop_allsubcategory', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function allSubCategory(Request $request, SubCategoryRepository $subCategoryRepository, $id): Response
    {
        $products = $this->paginator->paginate(
            $subCategoryRepository->find($id)->getProducts(),
            $request->query->getInt('page', 1),
            HasLimit::PRODUCT_LIMIT,
            ['wrap-queries' => true]
        );

        $subCategory = $subCategoryRepository->find($id);

        return $this->render('shop/categories.html.twig', compact('products', 'subCategory'));
    }
}
