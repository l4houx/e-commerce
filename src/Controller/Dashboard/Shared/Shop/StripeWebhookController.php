<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Entity\Traits\HasRoles;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

// #[IsGranted(HasRoles::ADMINAPPLICATION)]
// #[Route(path: '/%website_admin_dashboard_path%/manage-orders', name: 'admin_dashboard_order_')]
class StripeWebhookController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/stripe-webhook', name: 'stripe_webhook', methods: ['GET'])]
    public function webhook(Request $request, Event $event): Response
    {
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
            return new Response($this->translator->trans('Invalid payload.'), 400);
        } catch (SignatureVerificationException $e) {
            return new Response($this->translator->trans('Invalid signature.'), 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded': // Contains the object payment_intent
                $paymentIntent = $event->data->object;
                $filename = 'stripe-details-'.uniqid().'txt';
                file_put_contents($filename, $paymentIntent);
                break;
            case 'payment_method.attached': // Contains the object payment_method
                $paymentMethod = $event->data->object;
                break;
            default:
                echo 'Received unknown event type '.$event->type;
                break;
        }

        return new Response($this->translator->trans('Received event.'), 200);
    }
}
