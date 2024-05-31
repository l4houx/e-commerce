<?php

namespace App\Controller;

use App\Entity\Filter;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\SubCategory;
use App\Form\FilterFormType;
use App\Entity\Traits\HasLimit;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly PaginatorInterface $paginator,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/products', name: 'products', methods: ['GET'])]
    public function index(
        Request $request,
        ?SubCategory $subCategory,
    ): Response {
        $min = $this->productRepository->getMinPrice();
        $max = $this->productRepository->getMaxPrice();

        $filter = new Filter();
        $filter->min = $min;
        $filter->max = $max;

        $form = $this->createForm(FilterFormType::class, $filter)->handleRequest($request);

        $products = $this->productRepository->getForPagination(
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", HasLimit::PRODUCT_LIMIT),
            $request->query->get("sort", "new-products"),
            $subCategory,
            $filter
        );

        $pages = ceil(count($products) / $request->query->getInt("limit", HasLimit::PRODUCT_LIMIT));

        return $this->render("product/index.html.twig", [
            "products" => $products,
            "category" => $subCategory,
            "params" => array_merge(
                $request->query->all(),
                [
                    "category" => $subCategory?->getId(),
                    "page" =>  $request->query->getInt("page", 1),
                    "limit" => $request->query->getInt("limit", 18),
                    "sort" => $request->query->get("sort", "new-products")
                ]
            ),
            "form" => $form,
            "min" => $min,
            "max" => $max,
            "pages" => $pages,
            "pageRange" => range(
                max(1, $request->query->getInt("page", 1) - 3),
                min($pages, $request->query->getInt("page", 1) + 3)
            )
        ]);
    }

    /*
    public function products(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $products = $this->productRepository->findForPagination($page);

        //$products = $this->productRepository->findAll();
        //$categories = $this->categoryRepository->findAll();

        return $this->render('product/products.html.twig', compact('products'));
    }
    */

    #[Route(path: '/product/{id}', name: 'product', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function product(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $similarProducts = $this->productRepository->findSimilar($product);
        //$categories = $this->categoryRepository->findAll();

        if (!$product) {
            $this->addFlash('danger', $this->translator->trans('The product not be found'));

            return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
        }

        $product->viewed();
        $em->persist($product);
        $em->flush();

        return $this->render('product/product.html.twig', compact('product', 'similarProducts'));
    }

    #[Route(path: '/product/subcategory/{id}/filter', name: 'subcategory_filter', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function subCategoryFilter(Request $request, SubCategoryRepository $subCategoryRepository, $id): Response
    {
        $query = $subCategoryRepository->find($id)->getProducts();
        $page = $request->query->getInt('page', 1);

        $products = $this->paginator->paginate(
            $query,
            $page,
            HasLimit::PRODUCT_LIMIT,
            ['wrap-queries' => true]
        );

        //$products = $subCategoryRepository->find($id)->getProducts();
        $subCategory = $subCategoryRepository->find($id);
        //$categories = $this->categoryRepository->findAll();

        return $this->render('product/filter.html.twig', compact('products', 'subCategory'));
    }
}
