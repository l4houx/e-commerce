<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Repository\QuestionRepository;
use App\Repository\Shop\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/%website_dashboard_path%/customer', name: 'dashboard_customer_')]
class CartController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/cart', name: 'cart', methods: ['GET'])]
    public function cart(CartService $cartService): Response
    {
        return $this->render('dashboard/customer/cart/cart.html.twig', [
            'items' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
            'questions' => $this->questionRepository->findResults(6),
        ]);
    }

    #[Route(path: '/cart/add/{id}', name: 'cart_add', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function add(int $id, CartService $cartService): Response
    {
        $cartService->addToCart($id);

        return $this->redirectToRoute('dashboard_customer_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/remove/{id}', name: 'cart_remove', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->cartRemove($id);

        $this->addFlash('info', $this->translator->trans('Your cart has been updated.'));

        return $this->redirectToRoute('dashboard_customer_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/decrease/{id}', name: 'cart_decrease', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function decrease(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->cartDecrease($id);

        return $this->redirectToRoute('dashboard_customer_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/delete/{id}', name: 'cart_delete', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->cartDelete($id);

        //$this->addFlash('info', $this->translator->trans('Your cart has been deleted.'));

        return $this->redirectToRoute('dashboard_customer_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/empty', name: 'cart_empty', methods: ['GET'])]
    public function empty(CartService $cartService): RedirectResponse
    {
        $cartService->cartEmpty();
        $this->addFlash('info', $this->translator->trans('Your cart has been emptied.'));

        return $this->redirectToRoute('dashboard_customer_cart', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/cart/remove', name: 'cart_remove_all', methods: ['GET'])]
    public function removeCartAll(CartService $cartService): RedirectResponse
    {
        $cartService->removeCartAll();
        $this->addFlash('danger', $this->translator->trans('Cart was deleted successfully.'));

        return $this->redirectToRoute('shop');
    }
}
