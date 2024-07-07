<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use App\Event\HelpCenterSupportRequestEvent;
use App\Event\OrderConfirmationEmailEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly MailerInterface $mailer,
        private readonly Environment $templating,
        private readonly ParameterBagInterface $parameter
    ) {
    }

    public function onOrderConfirmationEmailEvent(OrderConfirmationEmailEvent $event): void
    {
        $order = $event->order;

        $html = $this->templating->render('mails/order-confirmation-email.html.twig', [
            'order' => $order,
        ]);

        $email = (new Email())
            ->from(new Address(
                $this->parameter->get('website_no_reply_email'),
                $this->parameter->get('website_name'),
            ))
            ->subject($this->translator->trans('Your orders bought from').' '.$this->parameter->get('website_name'))
            ->to(new Address($order->getEmail(), $order->getFullName()))
            ->html($html)
        ;
        $this->mailer->send($email);
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;

        $html = $this->templating->render('mails/contact.html.twig', [
            'data' => $data,
        ]);

        $email = (new Email())
            ->from(new Address(
                $data->service,
                $this->parameter->get('website_name'),
            ))
            ->subject($this->translator->trans('Request contact'))
            ->to(new Address($data->email))
            ->html($html)
        ;
        $this->mailer->send($email);
    }

    public function onHelpCenterSupportRequestEvent(HelpCenterSupportRequestEvent $event): void
    {
        $data = $event->data;

        $html = $this->templating->render('mails/support.html.twig', [
            'data' => $data,
        ]);

        $email = (new Email())
            ->from(new Address(
                $data->service,
                $this->parameter->get('website_name'),
            ))
            ->subject($this->translator->trans('Request support'))
            ->to(new Address($data->email))
            ->html($html)
        ;
        $this->mailer->send($email);
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $html = $this->templating->render('mails/login.html.twig', [
            'user' => $user,
        ]);

        $email = (new Email())
            ->from(new Address(
                $this->parameter->get('website_no_reply_email'),
                $this->parameter->get('website_name'),
            ))
            ->subject($this->translator->trans('Login'))
            ->to(new Address($user->getEmail()))
            ->html($html)
        ;
        $this->mailer->send($email);
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
