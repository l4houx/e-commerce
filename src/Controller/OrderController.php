<?php

namespace App\Controller;

use App\Entity\Shop\Order;
use App\Service\CartService;
use App\Entity\Shop\Shipping;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Form\Shop\OrderFormType;
use App\Repository\Shop\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Shop\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                    $order->setCreatedAt(new \DateTimeImmutable());
                    $order->setTotalPrice($cartService->getTotal());
                    $this->em->persist($order);
                    $this->em->flush();
                }

                $this->addFlash('success', $this->translator->trans('Content was created successfully.'));

                return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        $product = $this->productRepository->findBy([], ['id' => 'DESC'], 2);

        return $this->render('order/order.html.twig', [
            'items' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route(path: '/order/country/{id}/shipping-cost', name: 'order_shipping_cost')]
    public function shippingCost(Shipping $shipping): Response
    {
        $shippingPrice = $shipping->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => "on",  "content" => $shippingPrice]));
        // return new JsonResponse(['status' => 200, "message" => "on",  "content" => $shippingPrice]);
    }
}
