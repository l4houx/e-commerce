<?php

namespace App\Controller\Shop;

use App\Entity\Traits\HasRoles;
use App\Service\CompareService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/shop', name: 'shop_')]
#[IsGranted(HasRoles::SHOP)]
class CompareController extends AbstractController
{
    #[Route('/compare', name: 'compare')]
    public function compare(CompareService $compareService): Response
    {
        $compare = $compareService->getCompareDetails();
        $compareJson = json_encode($compare);

        return $this->render('shop/compare.html.twig', compact('compare', 'compareJson'));
    }

    #[Route('/compare/add/{productId}', name: 'add_to_compare')]
    public function addToCompare(string $productId, CompareService $compareService): Response
    {
        $compareService->addToCompare($productId);
        $compare = $compareService->getCompareDetails();

        return $this->json($compare);
    }

    #[Route('/compare/remove/{productId}', name: 'remove_to_compare')]
    public function removeToCompare(string $productId, CompareService $compareService): Response
    {
        $compareService->removeToCompare($productId);
        $compare = $compareService->getCompareDetails();

        return $this->json($compare);
    }

    #[Route('/compare/get', name: 'get_compare')]
    public function getCompare(CompareService $compareService): Response
    {
        $compare = $compareService->getCompareDetails();

        return $this->json($compare);
    }
}
