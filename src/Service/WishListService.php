<?php

namespace App\Service;

use App\Repository\Shop\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

readonly class WishListService
{
    public function __construct(
        private ProductRepository $productRepository,
        private RequestStack $requestStack
    ) {
    }

    public function getWishList()
    {
        return $this->getSession()->get('wishlist', []);
    }

    public function updateWishList($wishlist)
    {
        return $this->getSession()->set('wishlist', $wishlist);
    }

    public function addToWishList($productId): void
    {
        $wishlist = $this->getWishList();

        if (!isset($wishlist[$productId])) {
            $wishlist[$productId] = 1;
            $this->updateWishList($wishlist);
        }
    }

    public function removeToWishList($productId): void
    {
        $wishlist = $this->getWishList();

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            $this->updateWishList($wishlist);
        }
    }

    public function clearWishList(): void
    {
        $this->updateWishList([]);
    }

    public function getWishListDetails(): array
    {
        $wishlist = $this->getWishList();

        $result = [];

        foreach ($wishlist as $productId => $quantity) {
            $product = $this->productRepository->find($productId);
            if ($product) {
                $result[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'slug' => $product->getSlug(),
                    'imageUrls' => $product->getImageUrls(),
                    'salePrice' => $product->getSalePrice(),
                    'regularPrice' => $product->getRegularPrice(),
                    'stock' => $product->getStock(),
                ];
            } else {
                unset($wishlist[$productId]);
                $this->updateWishList($wishlist);
            }
        }

        return $result;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
