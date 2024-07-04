<?php

namespace App\Controller\Shop;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Traits\HasRoles;
use App\Repository\Shop\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted(HasRoles::TEAM)]
class InvoiceController extends AbstractController
{
    //#[Route(path: '/order-invoice/{id}', name: 'dashboard_orders_invoice', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function invoice(
        int $id,
        TranslatorInterface $translator,
        OrderRepository $orderRepository
    ): Response {
        $order = $orderRepository->find($id);

        if (!$order) {
            $this->addFlash('danger', $translator->trans('The order can not be found'));
            return $this->redirectToRoute("dashboard_account_order_index");
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('shop/order/invoice-pdf.html.twig', [
            'order' => $order,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($order->getRef() . "-" . $translator->trans("orders"), [
            "Attachment" => false
        ]);

        return $this->render('shop/order/invoice-pdf.html.twig', compact('order'));
    }
}
