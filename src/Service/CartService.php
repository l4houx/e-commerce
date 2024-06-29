<?php

namespace App\Service;

use App\Repository\Shop\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

readonly class CartService
{
    public function __construct(
        private ProductRepository $productRepository,
        private RequestStack $requestStack
    ) {
    }

    public function getCart(): array
    {
        $cart = $this->getSession()->get('cart', []);

        $items = [];

        if ($cart) {
            foreach ($cart as $id => $quantity) {
                $product = $this->productRepository->find($id);
                if (!$product) {
                    $this->cartRemove($id);
                    continue;
                }

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return $items;
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total;
    }

    public function addToCart(int $id): void
    {
        $cart = $this->getSession()->get('cart', []);

        if (empty($cart[$id])) {
            $cart[$id] = 1;
        } else {
            ++$cart[$id];
        }

        $this->getSession()->set('cart', $cart);
    }

    public function cartRemove(int $id)
    {
        $cart = $this->getSession()->get('cart', []);

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                --$cart[$id];
            } else {
                unset($cart[$id]);
            }
        }

        return $this->getSession()->set('cart', $cart);
    }

    public function cartDecrease(int $id)
    {
        $cart = $this->getSession()->get('cart', []);

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                --$cart[$id];
            } else {
                unset($cart[$id]);
            }
        }

        return $this->getSession()->set('cart', $cart);
    }

    public function cartDelete(int $id)
    {
        $cart = $this->getSession()->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        return $this->getSession()->set('cart', $cart);
    }

    public function cartEmpty()
    {
        return $this->getSession()->remove('cart');
    }

    public function removeCartAll()
    {
        return $this->getSession()->remove('cart');
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
