<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Entity\Shop\Order;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class StripeWebhookController extends AbstractController
{
    #[Route(path: '/stripe-webhook', name: 'stripe_webhook', methods: ['GET', 'POST'])]
    public function webhook(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response {
        new StripeClient($_ENV['STRIPE_SECRET']);
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);
        $endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return new Response($translator->trans('Invalid payload.'), 400);
        } catch (SignatureVerificationException $e) {
            return new Response($translator->trans('Invalid signature.'), 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded': // Contains the object payment_intent
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata->orderId;
                $order = $em->getRepository(Order::class)->find($orderId);

                $cartPrice = $order->getTotalPrice();
                $stripeTotalAmount = $paymentIntent->amount / 100;

                if ($cartPrice == $stripeTotalAmount) {
                    if (!$order->IsPaid()) {
                        $order->setIsPaid(true);
                        $order->setStatus(1);
                        $em->persist($order);
                        $em->flush();
                    }
                }
                break;
            case 'payment_method.attached': // Contains the object payment_method
                $paymentMethod = $event->data->object;
                break;
            default:
                // echo 'Received unknown event type '.$event->type;
                break;
        }

        return new Response($translator->trans('Received event.'), 200);
    }
}
