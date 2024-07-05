<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use App\Event\HelpCenterSupportRequestEvent;
use App\Event\OrderConfirmationEmailEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly MailerInterface $mailer,
        private readonly ParameterBagInterface $parameter
    ) {
    }

    public function onOrderConfirmationEmailEvent(OrderConfirmationEmailEvent $event): void
    {
        $order = $event->order;

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(new Address(
                    $this->parameter->get('website_no_reply_email'),
                    $this->parameter->get('website_name'),
                ))
                ->subject($this->translator->trans('Your orders bought from').' '.$this->parameter->get('website_name'))
                ->to(new Address($order->getEmail(), $order->getFullName()))
                ->htmlTemplate('mails/order-confirmation-email.html.twig')
                ->context(['order' => $order])
        );
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(new Address(
                    $data->service,
                    $this->parameter->get('website_name'),
                ))
                ->subject($this->translator->trans('Request contact'))
                ->to(new Address($data->email))
                ->htmlTemplate('mails/contact.html.twig')
                ->context(['data' => $data])
        );
    }

    public function onHelpCenterSupportRequestEvent(HelpCenterSupportRequestEvent $event): void
    {
        $data = $event->data;

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(new Address(
                    $data->service,
                    $this->parameter->get('website_name'),
                ))
                ->subject($this->translator->trans('Request support'))
                ->to(new Address($data->email))
                ->htmlTemplate('mails/support.html.twig')
                ->context(['data' => $data])
        );
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(new Address(
                    $this->parameter->get('website_no_reply_email'),
                    $this->parameter->get('website_name'),
                ))
                ->subject($this->translator->trans('Login'))
                ->to(new Address($user->getEmail()))
                ->htmlTemplate('mails/login.html.twig')
                ->context(['user' => $user])
        );
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            OrderConfirmationEmailEvent::class => 'onOrderConfirmationEmailEvent',
            ContactRequestEvent::class => 'onContactRequestEvent',
            HelpCenterSupportRequestEvent::class => 'onHelpCenterSupportRequestEvent',
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }
}
