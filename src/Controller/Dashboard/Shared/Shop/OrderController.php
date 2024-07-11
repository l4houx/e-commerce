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
use App\Service\StripeApiService;
use App\Repository\Shop\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\OrderConfirmationEmailEvent;
use App\Repository\Shop\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[IsGranted(HasRoles::SHOP)]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly EntityManagerInterface $em,
        private readonly ProductRepository $productRepository,
        private readonly OrderRepository $orderRepository,
        private readonly SettingService $settingService,
        private readonly UrlGeneratorInterface $generator,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/%website_dashboard_path%/customer/checkout', name: 'dashboard_customer_checkout', methods: ['GET', 'POST'])]
    public function checkout(Request $request, CartService $cartService, EventDispatcherInterface $eventDispatcher): Response
    {
        $order = new Order();
        // $order->setUsers($this->getUser());
        // $order->setRef($this->settingService->generateReference(4));

        $form = $this->createForm(OrderFormType::class, $order)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $dateTimeImmutable = new \DateTimeImmutable('now');
                $reference = $dateTimeImmutable->format('dmY').'-'.uniqid();

                if (!empty($cartService->getTotal())) {
                    $totalPrice = $cartService->getTotal() + $order->getCountrycode()->getShippingCost();
                    $order->setCreatedAt($dateTimeImmutable);
                    $order->setTotalPrice($totalPrice);
                    $order->setRef($reference);
                    $order->setIsPaid(false);
                    $this->em->persist($order);

                    foreach ($cartService->getCart() as $value) {
                        $orderDetail = new OrderDetail();
                        $orderDetail->setOrder($order);
                        $orderDetail->setProduct($value['product']);
                        $orderDetail->setQuantity($value['quantity']);
                        $order->addOrderDetail($orderDetail);
                        $this->em->persist($orderDetail);
                    }

                    $this->em->flush();

                    if ($order->isPayOnDelivery()) {
                        $cartService->removeCartAll();

                        $eventDispatcher->dispatch(new OrderConfirmationEmailEvent($order));

                        return $this->redirectToRoute('dashboard_customer_success', [], Response::HTTP_SEE_OTHER);
                    }

                    $shippingCost = $order->getCountrycode()->getShippingCost();

                    $buySuccess = $this->generator->generate('buy_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    $buyCancel = $this->generator->generate('buy_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

                    $stripe = new StripeApiService();
                    $stripe->createPaymentSession(
                        $cartService->getCart(), 
                        $shippingCost, 
                        $order->getId(),
                        $buySuccess,
                        $buyCancel
                    );

                    $redirectUrl = $stripe->getRedirectUrl();

                    return $this->redirect($redirectUrl);
                }
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
        $referer = $request->headers->get('referer');
        if (!\is_string($referer) || !$referer || $referer != "dashboard_customer_checkout_done") {
            return $this->redirectToRoute('dashboard_customer_orders', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/customer/order/failure.html.twig');
    }

    #[Route(path: '/%website_dashboard_path%/customer/my-order', name: 'dashboard_customer_orders', methods: ['GET'])]
    public function orders(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $orders = $this->orderRepository->findForPagination($page);

        return $this->render('dashboard/shared/shop/order/orders.html.twig', compact('orders'));
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{type}', name: 'admin_order_type', methods: ['GET'])]
    #[IsGranted(HasRoles::EDITOR)]
    public function ordersTypes(Request $request, $type): Response
    {
        if ($type == 'isCompleted') {
            $data = $this->orderRepository->findBy(['isCompleted' => 1], ['id' => 'DESC']);
        } elseif ($type == 'payOnStripeNotDelivered') {
            $data = $this->orderRepository->findBy(['isCompleted' => 0, 'isPayOnDelivery' => 0, 'isPaid' => 1, 'status' => 1], ['id' => 'DESC']);
        } elseif ($type == 'payOnStripeIsDelivered') {
            $data = $this->orderRepository->findBy(['isCompleted' => 1, 'isPayOnDelivery' => 0, 'isPaid' => 1, 'status' => 1], ['id' => 'DESC']);
        } elseif ($type == 'payOnIsDelivered') {
            $data = $this->orderRepository->findBy(['isCompleted' => 1, 'isPayOnDelivery' => 1, 'isPaid' => 0, 'status' => 1], ['id' => 'DESC']);
        } elseif ($type == 'payOnNotDelivered') {
            $data = $this->orderRepository->findBy(['isCompleted' => 0, 'isPayOnDelivery' => 1, 'isPaid' => 0, 'status' => 1], ['id' => 'DESC']);
        }

        $orders = $this->paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            10
        );

        //$page = $request->query->getInt('page', 1);
        //$orders = $this->orderRepository->findForPagination($page);

        return $this->render('dashboard/editor/shop/order/orders.html.twig', compact('orders'));
    }

    #[Route('/%website_dashboard_path%/customer/my-order/{id}', name: 'dashboard_customer_order_details', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function details(Order $order): Response
    {
        return $this->render('dashboard/shared/shop/order/details.html.twig', compact('order'));
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/cancel', name: 'admin_order_cancel', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(HasRoles::ADMINAPPLICATION)]
    public function cancel(int $id): Response
    {
        $order = $this->settingService->getOrders(['id'=> $id, 'status' => 'all'])->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('danger', $this->translator->trans('The order not be found'));

            return $this->settingService->redirectToReferer('orders');
        }

        if ($order->getStatus() != 0 && $order->getStatus() != 1) {
            $this->addFlash('danger', $this->translator->trans('The order status must be paid or awaiting payment'));

            return $this->settingService->redirectToReferer('orders');
        }

        $this->addFlash('danger', $this->translator->trans('The order has been permanently canceled'));

        return $this->settingService->redirectToReferer('orders');
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/delete', name: 'admin_order_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Order $order): Response
    {
        if ($this->isCsrfTokenValid('order_deletion_'.$order->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($order);
            $this->em->flush();

            $this->addFlash('danger', $this->translator->trans('The order has been deleted'));
        }

        return $this->redirectToRoute('dashboard_customer_orders', [], Response::HTTP_SEE_OTHER);
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

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/resend-confirmation-email', name: 'admin_order_resend_confirmation_email', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(HasRoles::ADMINAPPLICATION)]
    public function resendConfirmationEmail(Request $request, int $id): Response
    {
        $order = $this->settingService->getOrders(['id'=> $id, 'status' => 'all'])->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('danger', $this->translator->trans('The order not be found'));

            return $this->settingService->redirectToReferer('orders');
        }

        $this->settingService->sendOrderConfirmationEmail($order, $request->query->get('email'));

        $this->addFlash('success', $this->translator->trans('The confirmation email has been resent to') . ' ' . $request->query->get('email'));

        return $this->settingService->redirectToReferer('orders');
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/validate', name: 'admin_order_validate', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(HasRoles::ADMINAPPLICATION)]
    public function validate(int $id): Response
    {
        $order = $this->settingService->getOrders(['id'=> $id, 'status' => 0])->getQuery()->getOneOrNullResult();
        if (!$order) {
            $this->addFlash('danger', $this->translator->trans('The order not be found'));

            return $this->settingService->redirectToReferer('orders');
        }

        $this->addFlash('success', $this->translator->trans('The order has been successfully validated'));

        return $this->settingService->redirectToReferer('orders');
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/completed', name: 'admin_order_completed', methods: ['GET'])]
    #[IsGranted(HasRoles::EDITOR)]
    public function isCompleted(Request $request, int $id): Response
    {
        $order = $this->em->getRepository(Order::class)->find($id);
        $order->setIsCompleted(true);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Modification made successfully.'));

        return $this->redirect($request->headers->get('referer'));
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
