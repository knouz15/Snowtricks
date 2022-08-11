<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;


class MailerController extends AbstractController
{
    /**
     * @var MailerInterface
     */
    private $mailer;

/** 
 * @Route("/email")
 * */

public function sendEmail(MailerInterface $mailer)
    { 
        
	$email = (new TemplatedEmail())
            ->from('resgister@example.fr')
            ->to('alex@example.com')
            ->subject('Validez votre inscription!')
            ->text("Bienvenue !")
            ->htmlTemplate('emails/registration.html.twig');
    $mailer->send($email);

 

return new Response(
          'Email was sent'
       );

    }
}


