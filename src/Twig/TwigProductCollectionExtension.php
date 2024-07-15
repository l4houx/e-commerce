<?php

namespace App\Twig;

use App\Repository\Shop\ProductCollectionRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigProductCollectionExtension extends AbstractExtension
{
    public function __construct(private readonly ProductCollectionRepository $productCollectionRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getMegaCollections', $this->getMegaCollections(...)),
        ];
    }

    public function getMegaCollections(): array
    {
        return $this->productCollectionRepository->findBy(['isMegaMenu' => true]);
    }
}
