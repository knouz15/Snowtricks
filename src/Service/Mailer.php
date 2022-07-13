<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer {
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($email, $token)
    {
        $email = (new TemplatedEmail())
            ->from('resgister@example.fr')
            ->to(new Address($email))
            // ->subject('Merci pour votre inscription!')
            ->subject('Validez votre inscription!')
            // ->text("Bienvenue {$user->username()}!")
            // ->html('<h1>See Twig integration for better HTML integration!</h1>');
            // ->html('<p>See Twig integration for better HTML integration!</p>');

            // path of the Twig template to render
            ->htmlTemplate('emails/registration.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'token' => $token,
            ])
        ;

        $this->mailer->send($email);
        dd($email);
    }
}