<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Entity\Traits\HasRoles;
use App\Repository\Shop\OrderRepository;
use App\Repository\Shop\ProductRepository;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::DEFAULT)]
#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository,
        private readonly SettingService $settingService,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/my-orders', name: 'order_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('dashboard/shared/shop/order/index.html.twig');
    }
}
