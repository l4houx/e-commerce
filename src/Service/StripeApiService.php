<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeApiService
{
    private string $redirectUrl;

    public function __construct()
    {
        Stripe::setApiVersion('2024-06-20');
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);
    }

    public function createPaymentSession($cartService, $shippingCost, $orderId, $buySuccess, $buyCancel): ?string
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
            'cancel_url' => $buyCancel,
            'success_url' => $buySuccess,
            //'cancel_url' => 'https://127.0.0.1:8001/en/buy-cancel',
            //'success_url' => 'https://127.0.0.1:8001/en/buy-success',
            'mode' => 'payment',
            'payment_method_types' => [
                'card',
            ],
            'metadata' => [
                'orderId'=> $orderId,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'orderId'=> $orderId,
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
            //'customer' => '',
            'allow_promotion_codes' => true,
        ]);

        return $this->redirectUrl = $checkout_session->url;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}
