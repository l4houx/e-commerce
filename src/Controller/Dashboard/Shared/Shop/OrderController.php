<?php

namespace App\Controller\Dashboard\Shared\Shop;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Repository\Shop\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Shop\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted(HasRoles::DEFAULT)]
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

    #[Route(path: '/%website_dashboard_path%/account/my-orders', name: 'dashboard_account_order_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $orders = $this->orderRepository->findForPagination($page);

        return $this->render('dashboard/shared/shop/order/index.html.twig', compact("orders"));
    }

    #[Route(path: '/order-invoice/{id}', name: 'dashboard_orders_invoice', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function invoice(Request $request, int $id): Response
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            $this->addFlash('danger', $this->translator->trans('The order can not be found'));
            return $this->redirectToRoute("dashboard_account_order_index");
        }

        if ($request->getLocale() == "ar") {
            return $this->redirectToRoute("dashboard_orders_invoice", ["id" => $id, "_locale" => "en"]);
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('dashboard/shared/shop/order/invoice-pdf.html.twig', [
            'order' => $order,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream(
            $this->getParameter('website_slug')."-".
            $this->translator->trans("invoice-").$order->getId().'.pdf',
            [
            "Attachment" => false
            ]
        );

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
