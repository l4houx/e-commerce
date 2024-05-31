<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Shipping;
use App\Entity\Traits\HasRoles;
use App\Form\OrderFormType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private readonly OrderRepository $orderRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/order', name: 'order', methods: ['GET'])]
    public function order(Request $request, CartService $cartService): Response
    {
        $order = new Order();
        // $order->setUsers($this->getUser());
        $order->setRef(uniqid());

        $form = $this->createForm(OrderFormType::class, $order)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($order);
                $this->em->flush();

                $this->addFlash('success', $this->translator->trans('Content was created successfully.'));

                return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
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
