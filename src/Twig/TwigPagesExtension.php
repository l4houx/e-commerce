<?php

namespace App\Twig;

use App\Repository\Settings\PageRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPagesExtension extends AbstractExtension
{
    public function __construct(private readonly PageRepository $pageRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('headerPages', $this->getHeaderPages(...)),
            new TwigFunction('footerPages', $this->getFooterPages(...)),
        ];
    }

    public function getHeaderPages(): array
    {
        return $this->pageRepository->findBy(['isHeader' => true]);
    }

    public function getFooterPages(): array
    {
        return $this->pageRepository->findBy(['isFooter' => true]);
    }
}
