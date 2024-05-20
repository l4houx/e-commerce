<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Traits\HasRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::ADMINAPPLICATION)]
#[Route('/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_')]
class ProductController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route(path: '/products', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {


        return $this->render('dashboard/admin/products/product/index.html.twig');
    }

    #[Route(path: '/products/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/products/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {


        return $this->render('dashboard/admin/products/product/new-edit.html.twig');
    }
}
