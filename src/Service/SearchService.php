<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;

readonly class SearchService
{
    public function __construct(
        private ProductService $productService,
        private RouterInterface $router
    ) {
    }

    public function search(string $keyword): array
    {
        if ('' === $keyword) {
            return [];
        }

        $productOptions = [];

        $products = $this->productService->findByKeyword($keyword);

        foreach ($products as $product) {
            $productOptions[] = [
                'label' => "[{$product['id']}] {$product['content']}",
                'value' => $this->router->generate('product_show', [
                    'id' => $product['id'],
                ]),
            ];
        }

        return [
            'products' => $productOptions,
        ];
    }
}
