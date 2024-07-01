<?php

namespace App\Controller\Shop;

use App\Entity\Shop\Order;
use App\Entity\Shop\OrderDetail;
use App\Entity\Shop\Shipping;
use App\Entity\Traits\HasRoles;
use App\Form\Shop\OrderFormType;
use App\Repository\Shop\ProductRepository;
use App\Service\CartService;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::DEFAULT)]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProductRepository $productRepository,
        private readonly SettingService $settingService,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/order', name: 'order', methods: ['GET', 'POST'])]
    public function order(Request $request, CartService $cartService): Response
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

                        foreach ($cartService->getCart() as $key => $value) {
                            $orderDetail = new OrderDetail();
                            $orderDetail->setOrder($order);
                            $orderDetail->setProduct($value['product']);
                            $orderDetail->setQuantity($value['quantity']);
                            $this->em->persist($orderDetail);
                            $this->em->flush();
                        }
                    }

                    $cartService->removeCartAll();
                }

                // $this->addFlash('success', $this->translator->trans('Order was created successfully.'));

                return $this->redirectToRoute('order_ok_message', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        $product = $this->productRepository->findBy([], ['id' => 'DESC'], 2);

        return $this->render('shop/order/order.html.twig', [
            'items' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route(path: '/order-ok-message', name: 'order_ok_message', methods: ['GET'])]
    public function orderOkMessage(): Response
    {
        return $this->render('shop/order/order-ok-message.html.twig');
    }

    #[Route(path: '/order/country/{id}/shipping-cost', name: 'order_shipping_cost')]
    public function shippingCost(Shipping $shipping): Response
    {
        $shippingPrice = $shipping->getShippingCost();

        return new Response(json_encode(['status' => 200, 'message' => 'on',  'content' => $shippingPrice]));
        // return new JsonResponse(['status' => 200, "message" => "on",  "content" => $shippingPrice]);
    }
}
