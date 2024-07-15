<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Repository\Shop\CategoryRepository;
use App\Repository\Shop\SubCategoryRepository;

class TwigCategoryExtension extends AbstractExtension
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly SubCategoryRepository $subCategoryRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getSubCategories', $this->getSubCategories(...)),
            new TwigFunction('getAllCategories', $this->getAllCategories(...)),
            new TwigFunction('getMegaMenuCategories', $this->getMegaMenuCategories(...)),
            new TwigFunction('getMegaMenuSubCategories', $this->getMegaMenuSubCategories(...)),
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

    public function getMegaMenuCategories(): array
    {
        return $this->categoryRepository->findBy(['isMegaMenu' => true]);
    }

    public function getMegaMenuSubCategories(): array
    {
        return $this->subCategoryRepository->findBy(['isMegaMenu' => true]);
    }
}
