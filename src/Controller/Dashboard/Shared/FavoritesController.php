<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\Shop\Product;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Shop\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class FavoritesController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductRepository $productRepository,
        private readonly TranslatorInterface $translator,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '/my-favorites', name: 'favorites_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUserOrThrow();

        $products = $this->productRepository->findForFavoritesPagination(
            //$addedtofavoritesby,
            $request->query->getInt("page", 1),
        );

        /*$products = $paginator->paginate(
            $this->settingService->getProducts(['addedtofavoritesby' => $this->getUser()])->getQuery(),
            $request->query->getInt('page', 1),
            12,
            ['wrap-queries' => true]
        );*/

        return $this->render('dashboard/shared/product/favorites.html.twig', compact("products", "user"));
    }

    #[Route(path: '/my-favorites/new/{slug}', name: 'favorites_new', requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[Route(path: '/my-favorites/remove/{slug}', name: 'favorites_remove', methods: ['POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function newRemove(string $slug): JsonResponse
    {
        /** @var Product $product */
        //$product = $this->productRepository->find(['slug' => $slug]);
        $product = $this->settingService->getProducts(['slug' => $slug])->getQuery()->getOneOrNullResult();
        if (!$product) {
            return new JsonResponse(['danger' => $this->translator->trans('The product can not be found')]);
        }

        $user = $this->getUserOrThrow();
        if ($product->isAddedToFavoritesBy($user)) {
            $user->removeFavorite($product);

            $this->em->persist($user);
            $this->em->flush();

            return new JsonResponse(['danger' => $this->translator->trans('The product has been removed from your favorites')]);
        } else {
            $this->getUser()->addFavorite($product);

            $this->em->persist($user);
            $this->em->flush();

            return new JsonResponse(['success' => $this->translator->trans('The product has been added to your favorites')]);
        }
    }
}
