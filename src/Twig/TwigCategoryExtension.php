<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Repository\Shop\CategoryRepository;

class TwigCategoryExtension extends AbstractExtension
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getSubCategories', $this->getSubCategories(...)),
            new TwigFunction('getAllCategories', $this->getAllCategories(...)),
        ];
    }

    public function getSubCategories(): array
    {
        return $this->categoryRepository->getSubCategories(8);
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
}
