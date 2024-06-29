<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\Traits\HasRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class CartController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/my-cart', name: 'cart_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/shared/order/cart/index.html.twig');
    }

    #[Route(path: '/my-cart/add', name: 'cart_add', methods: ['GET', 'POST'])]
    public function add(): Response
    {
        return $this->render('dashboard/shared/order/cart/add.html.twig');
    }

    #[Route(path: '/my-cart/{id}/remove', name: 'cart_remove', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(): Response
    {
        return $this->redirectToRoute('dashboard_account_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/my-cart/empty', name: 'cart_empty', methods: ['GET'])]
    public function empty(): Response
    {
        $this->addFlash('info', $this->translator->trans('Your cart has been emptied'));

        return $this->redirectToRoute('dashboard_account_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
