<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\ProductRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    /*
    #[Route(path: '/cart', name: 'cart_index', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $cartWhitData = [];

        foreach ($cart as $id => $quantity) {
            $cartWhitData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWhitData));

        //dd($cartWhitData);
        //dd($total);

        return $this->render('cart/index.html.twig', [
            'items' => $cartWhitData,
            'total' => $total,
        ]);
    }

    #[Route(path: '/cart/add/{id}', name: 'cart_add', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function adds(SessionInterface $session, int $id): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            ++$cart[$id];
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
    }
    */

    #[Route(path: '/cart', name: 'cart', methods: ['GET'])]
    public function cart(CartService $cartService): Response
    {
        return $this->render('cart/cart.html.twig', [
            'items' => $cartService->getCart(),
            'total'=> $cartService->getTotal(),
            'questions' => $this->questionRepository->findResults(6)
        ]);
    }

    #[Route(path: '/cart/add/{id}', name: 'cart_add', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function add(int $id, CartService $cartService): Response
    {
        $cartService->addToCart($id);

        return $this->redirectToRoute('cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/remove/{id}', name: 'cart_remove', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->cartRemove($id);

        // $this->addFlash('info',$this->translator->trans('Content was deleted successfully.'));

        return $this->redirectToRoute('cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/decrease/{id}', name: 'cart_decrease', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function decrease(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->cartDecrease($id);

        return $this->redirectToRoute('cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/delete/{id}', name: 'cart_delete', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->cartDelete($id);

        $this->addFlash('info', $this->translator->trans('Cart was deleted successfully.'));

        return $this->redirectToRoute('cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/empty', name: 'cart_empty', methods: ['GET'])]
    public function empty(CartService $cartService): RedirectResponse
    {
        $cartService->cartEmpty();
        $this->addFlash('success', $this->translator->trans('Your cart is currently empty'));

        return $this->redirectToRoute('cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/remove', name: 'cart_remove_all', methods: ['GET'])]
    public function removeCartAll(CartService $cartService): RedirectResponse
    {
        $cartService->removeCartAll();
        $this->addFlash('danger', $this->translator->trans('Cart was deleted successfully.'));

        return $this->redirectToRoute('products');
    }
}
