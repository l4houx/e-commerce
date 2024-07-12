<?php

namespace App\Twig\Components;

use App\Entity\Shop\Product;
use App\Repository\Shop\ProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'search-engine')]
final class SearchEngineComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }

    /**
     * @return array<Product>
     */
    public function getProducts(): array
    {
        return $this->productRepository->findBySearchQuery($this->query);
    }

    public function getCountProduct(): int
    {
        return $this->productRepository->count([]);
    }
}
