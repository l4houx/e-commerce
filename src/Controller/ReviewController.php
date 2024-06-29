<?php

namespace App\Controller;

use App\Entity\Shop\Product;
use App\Service\SettingService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewController extends BaseController
{
    #[Route(path: '/shop/{slug}/reviews', name: 'shop_reviews', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function review(
        Request $request,
        PaginatorInterface $paginator,
        TranslatorInterface $translator,
        SettingService $settingService,
        string $slug
    ): Response {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');

        /** @var Product $product */
        $product = $settingService->getProducts(['slug' => $slug, 'elapsed' => 'all'])->getQuery()->getOneOrNullResult();
        if (!$product) {
            $this->addFlash('danger', $translator->trans('The product not be found'));

            return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
        }

        $reviews = $paginator->paginate(
            $settingService->getReviews(['shop_product' => $product->getSlug(), 'keyword' => $keyword])->getQuery(),
            $request->query->getInt('page', 1),
            $settingService->getSettings('reviews_per_page'),
            ['wrap-queries' => true]
        );

        return $this->render('shop/review.html.twig', compact('product', 'reviews'));
    }
}
