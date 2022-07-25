<?php

namespace App\Service;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;



//class Mailer {
class Mailer
{
    /**
     * @var MailerInterface
     */


    private $mailer;

    public function __construct(MailerInterface $mailer){$this->mailer = $mailer;}


/** 
 * @Route("/email")
 * */

public function sendEmail($email, $token)
    { 
	$email = (new TemplatedEmail())
            ->from('resgister@example.fr')
            ->to(new Address($email,$email))
            ->subject('Validez votre inscription!')
            ->text("Bienvenue !")
            ->htmlTemplate('emails/registration.html.twig')
            ->context(['token' => $token
        ]);
//On envoie le mail 
        $this->mailer->send($email);
    }
}