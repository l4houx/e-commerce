<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeApiService
{
    private string $redirectUrl;

    private readonly UrlGeneratorInterface $generator;

    public function __construct()
    {
        Stripe::setApiVersion('2024-06-20');
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);
    }

    public function createPaymentSession($cartService, $shippingCost): ?string
    {
        $carts = $cartService;
        $products = [
            [
                'name' => 'shipping cost',
                'price' => $shippingCost,
                'quantity' => 1,
            ],
        ];

        foreach ($carts as $value) {
            $productItem = [];
            $productItem['name'] = $value['product']->getName();
            $productItem['price'] = $value['product']->getPrice();
            $productItem['quantity'] = $value['quantity'];
            $products[] = $productItem;
        }

        $checkout_session = Session::create([
            'cancel_url' => 'https://127.0.0.1:8001/en/buy-cancel',
            'success_url' => 'https://127.0.0.1:8001/en/buy-success',
            'mode' => 'payment',
            'payment_method_types' => [
                'card',
            ],
            'metadata' => [
            ],
            'payment_intent_data' => [
                'metadata' => [
                ],
            ],
            'line_items' => [
                array_map(fn (array $product) => [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product['name'],
                        ],
                        'unit_amount' => $product['price'] * 100,
                    ],
                    'quantity' => $product['quantity'],
                ], $products),
            ],
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR'],
            ],
        ]);

        return $this->redirectUrl = $checkout_session->url;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function getRedirectSuccessUrl(): string
    {
        $url = $this->generator->generate('buy_success', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $url;
    }

    public function getRedirectCancelUrl(): string
    {
        $url = $this->generator->generate('buy_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $url;
    }
}
