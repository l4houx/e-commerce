<?php

namespace App\Controller\Dashboard\Shared\Shop;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Shop\Order;
use App\Service\CartService;
use App\Entity\Shop\Shipping;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Entity\Shop\OrderDetail;
use App\Form\Shop\OrderFormType;
use App\Service\SendMailService;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Repository\Shop\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\OrderConfirmationEmailEvent;
use App\Repository\Shop\ProductRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[IsGranted(HasRoles::SHOP)]
// #[Route(path: '/%website_dashboard_path%')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository,
        private readonly SettingService $settingService,
        private readonly TranslatorInterface $translator
    ) {
    }

    /** @throws TransportExceptionInterface */
    #[Route(path: '/%website_dashboard_path%/customer/checkout', name: 'dashboard_customer_checkout', methods: ['GET', 'POST'])]
    public function checkout(Request $request, CartService $cartService, EventDispatcherInterface $eventDispatcher): Response
    {
        $order = new Order();
        // $order->setUsers($this->getUser());
        $order->setRef($this->settingService->generateReference(4));

        $form = $this->createForm(OrderFormType::class, $order)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($order->isPayOnDelivery()) {
                    if (!empty($cartService->getTotal())) {
                        $order->setCreatedAt(new \DateTimeImmutable());
                        $order->setTotalPrice($cartService->getTotal());
                        $this->em->persist($order);
                        $this->em->flush();

                        foreach ($cartService->getCart() as $value) {
                            $orderDetail = new OrderDetail();
                            $orderDetail->setOrder($order);
                            $orderDetail->setProduct($value['product']);
                            $orderDetail->setQuantity($value['quantity']);
                            $order->addOrderDetail($orderDetail);
                            $this->em->persist($orderDetail);
                            $this->em->flush();
                        }
                    }

                    $cartService->removeCartAll();
                }

                /*
                $html = $this->renderView('mails/order-confirmation-email.html.twig', [
                    'order' => $order,
                ]);

                $email = (new Email())
                    ->from(new Address(
                        $this->getParameter('website_no_reply_email'),
                        $this->getParameter('website_name'),
                    ))
                    ->to(new Address($order->getEmail(), $order->getFullName()))
                    ->subject($this->translator->trans('Your orders bought from'). " " . $this->getParameter('website_name'))
                    ->html($html)
                ;

                $this->mailer->send($email);
                */

                $eventDispatcher->dispatch(new OrderConfirmationEmailEvent($order));

                return $this->redirectToRoute('dashboard_customer_success', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        $product = $this->productRepository->findBy([], ['id' => 'DESC'], 2);

        return $this->render('dashboard/customer/order/checkout.html.twig', [
            'items' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route(path: '/%website_dashboard_path%/customer/checkout/failure/{number}', name: 'dashboard_customer_checkout_failure', methods: ['GET'])]
    public function failure(Request $request, int $number): Response
    {
        return $this->render('dashboard/customer/order/failure.html.twig');
    }

    #[Route(path: '/%website_dashboard_path%/customer/my-order', name: 'dashboard_customer_orders', methods: ['GET'])]
    public function orders(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $orders = $this->orderRepository->findForPagination($page);

        return $this->render('dashboard/shared/shop/order/orders.html.twig', compact('orders'));
    }

    #[Route('/%website_dashboard_path%/customer/my-order/{id}', name: 'dashboard_customer_order_details', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function details(Order $order): Response
    {
        return $this->render('dashboard/shared/shop/order/details.html.twig', compact('order'));
    }

    #[Route(path: '/customer-invoice/{id}', name: 'dashboard_customer_invoice', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function invoice(Request $request, int $id): Response
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            $this->addFlash('danger', $this->translator->trans('The order can not be found'));

            return $this->redirectToRoute('dashboard_customer_orders');
        }

        if ('ar' == $request->getLocale()) {
            return $this->redirectToRoute('dashboard_customers_invoice', ['id' => $id, '_locale' => 'en']);
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('dashboard/shared/shop/order/customers-pdf.html.twig', [
            'order' => $order,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream(
            $this->getParameter('website_slug').'-'.
            $this->translator->trans('invoice-').$order->getId().'.pdf',
            [
                'Attachment' => false,
            ]
        );

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    #[Route(path: '/%website_admin_dashboard_path%/manage-orders/{id}/resend-confirmation-email', name: 'admin_dashboard_order_resend_confirmation_email', methods: ['GET'])]
    public function resendConfirmationEmail(Request $request, int $id): void
    {
    }

    #[Route(path: '/%website_dashboard_path%/order-shipping-cost/{id}', name: 'dashboard_shipping_cost')]
    public function shippingCost(Shipping $shipping): Response
    {
        $shippingPrice = $shipping->getShippingCost();

        return new Response(json_encode(['status' => 200, 'message' => 'on',  'content' => $shippingPrice]));
    }

    #[Route(path: '/%website_dashboard_path%/customer/success', name: 'dashboard_customer_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('dashboard/shared/shop/order/success.html.twig');
    }
}
