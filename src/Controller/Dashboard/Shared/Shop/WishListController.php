<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Entity\Traits\HasRoles;
use App\Service\WishListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::SHOP)]
class WishListController extends AbstractController
{
    #[Route('/wishlist', name: 'wishlist_index')]
    public function wishlist(WishListService $wishlistService): Response
    {
        $wishlist = $wishlistService->getWishListDetails();
        $wishlistJson = json_encode($wishlist);

        return $this->render('dashboard/shared/shop/wishlist.html.twig', compact('wishlist', 'wishlistJson'));
    }

    #[Route('/wishlist/add/{productId}', name: 'add_to_wishlist')]
    public function addToWishList(string $productId, WishListService $wishlistService): Response
    {
        $wishlistService->addToWishList($productId);
        $wishlist = $wishlistService->getWishListDetails();

        return $this->json($wishlist);
    }

    #[Route('/wishlist/remove/{productId}', name: 'remove_to_wishlist')]
    public function removeToWishList(string $productId, WishListService $wishlistService): Response
    {
        $wishlistService->removeToWishList($productId);
        $wishlist = $wishlistService->getWishListDetails();

        return $this->json($wishlist);
    }

    #[Route('/wishlist/get', name: 'get_wishlist')]
    public function getWishList(WishListService $wishlistService): Response
    {
        $wishlist = $wishlistService->getWishListDetails();

        return $this->json($wishlist);
    }
}
