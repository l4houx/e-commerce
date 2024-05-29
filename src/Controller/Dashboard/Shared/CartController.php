<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\Traits\HasRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/%website_dashboard_path%/user', name: 'dashboard_user_client_')]
#[IsGranted(HasRoles::DEFAULT)]
class CartController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/client-cart', name: 'cart_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/shared/client/cart/index.html.twig');
    }

    #[Route(path: '/client-cart/add', name: 'cart_add', methods: ['GET', 'POST'])]
    public function add(): Response
    {
        return $this->render('dashboard/shared/client/cart/add.html.twig');
    }

    #[Route(path: '/client-cart/{id}/remove', name: 'cart_remove', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(): Response
    {
        return $this->redirectToRoute('dashboard_user_client_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/client-cart/empty', name: 'cart_empty', methods: ['GET'])]
    public function empty(): Response
    {
        $this->addFlash('info', $this->translator->trans('Your cart has been emptied'));

        return $this->redirectToRoute('dashboard_user_client_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
