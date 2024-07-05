<?php

namespace App\Controller\Dashboard\Customer;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route(path: '/%website_dashboard_path%/customer', name: 'dashboard_customer_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute("dashboard_customer_orders");
    }
}
