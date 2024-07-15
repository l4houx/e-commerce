<?php

namespace App\Service;

use App\Repository\Shop\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

readonly class CompareService
{
    public function __construct(
        private ProductRepository $productRepository,
        private RequestStack $requestStack
    ) {
    }

    public function getCompare()
    {
        return $this->getSession()->get('compare', []);
    }

    public function updateCompare($compare)
    {
        return $this->getSession()->set('compare', $compare);
    }

    public function addToCompare($productId): void
    {
        $compare = $this->getCompare();

        if (!isset($compare[$productId])) {
            $compare[$productId] = 1;
            $this->updateCompare($compare);
        }
    }

    public function removeToCompare($productId): void
    {
        $compare = $this->getCompare();

        if (isset($compare[$productId])) {
            unset($compare[$productId]);
            $this->updateCompare($compare);
        }
    }

    public function clearCompare(): void
    {
        $this->updateCompare([]);
    }

    public function getCompareDetails(): array
    {
        $compare = $this->getCompare();

        $result = [];

        foreach ($compare as $productId => $quantity) {
            $product = $this->productRepository->find($productId);
            if ($product) {
                $result[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'slug' => $product->getSlug(),
                    'imageUrls' => $product->getImageUrls(),
                    'salePrice' => $product->getSalePrice(),
                    'regularPrice' => $product->getRegularPrice(),
                ];
            } else {
                unset($compare[$productId]);
                $this->updateCompare($compare);
            }
        }

        return $result;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
