<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendMailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ParameterBagInterface $parameter
    ) {
    }

    /** @throws TransportExceptionInterface */
    public function send(
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        // We create the email
        $email = (new TemplatedEmail())
            ->from(new Address(
                $this->parameter->get('website_no_reply_email'),
                $this->parameter->get('website_name'),
            ))
            ->to(new Address($to))
            ->subject($subject)
            ->htmlTemplate("mails/$template.html.twig")
            ->context($context)
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $transport) {
            throw $transport;
        }
    }
}
