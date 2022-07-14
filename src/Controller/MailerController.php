<?php

//namespace App\Service;
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;

//class Mailer {
class MailerController extends AbstractController
{
    /**
     * @var MailerInterface
     */


    private $mailer;

    //public function __construct(MailerInterface $mailer){$this->mailer = $mailer;}

    //public function sendEmail($email, $token)

/** 
 * @Route("/email")
 * */

public function sendEmail(MailerInterface $mailer)
    { 
        $email = (new Email())
	//$email = (new TemplatedEmail())
            ->from('resgister@example.fr')
            //->to(new Address($email))
->to('alex@example.com')
            // ->subject('Merci pour votre inscription!')
            ->subject('Validez votre inscription!')
            ->text("Bienvenue !");
            // ->html('<h1>See Twig integration for better HTML integration!</h1>');
            // ->html('<p>See Twig integration for better HTML integration!</p>');

            // path of the Twig template to render
            //->htmlTemplate('emails/registration.html.twig')

            // pass variables (name => value) to the template
            //->context(['token' => $token,]);

        // $this->mailer->send($email);
$mailer->send($email);



return new Response(
          'Email was sent'
       );
        dd($email);
    }
}