<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class BuyController extends AbstractController
{
    #[Route(path: '/buy-success', name: 'buy_success', methods: ['GET'])]
    public function success(CartService $cartService): Response
    {
        $cartService->removeCartAll();

        return $this->render('dashboard/customer/order/buy-success.html.twig');
    }

    #[Route(path: '/buy-cancel', name: 'buy_cancel', methods: ['GET'])]
    public function cancel(TranslatorInterface $translator): Response
    {
        $this->addFlash('danger', $translator->trans('Your purchase has been canceled.'));

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
