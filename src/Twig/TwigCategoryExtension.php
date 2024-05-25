<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigCategoryExtension extends AbstractExtension
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('categories', $this->categories(...)),
        ];
    }

    public function categories(): array
    {
        return $this->categoryRepository->findAll();
    }
}
