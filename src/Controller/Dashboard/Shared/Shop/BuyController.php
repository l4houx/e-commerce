<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Entity\Traits\HasRoles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// #[IsGranted(HasRoles::SHOP)]
// #[Route(path: '/%website_dashboard_path%/customer', name: 'dashboard_customer_')]
class BuyController extends AbstractController
{
    #[Route(path: '/buy-success', name: 'buy_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('dashboard/customer/order/buy-success.html.twig');
    }

    #[Route(path: '/buy-cancel', name: 'buy_cancel', methods: ['GET'])]
    public function cancel(TranslatorInterface $translator): Response
    {
        $this->addFlash('danger', $translator->trans('Your purchase has been canceled.'));

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
