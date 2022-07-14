<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer;
use App\Form\RegistrationFormType;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mime\Email;

class RegistrationController extends AbstractController
{

    private $mailer;
    private $userRepository;
    public function __construct(
        // Mailer $mailer,
     UserRepository $userRepository)
    {
        // $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    #[Route('/inscription', name: 'app_register', methods: ['GET','POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        UsersAuthenticator $authenticator, 
        EntityManagerInterface $entityManager
        ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $user = $form-> getData();
            //dd($user);
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // dd($user);
            $user->setIagreeTerms(new \DateTime()); 
            $user->setToken($this->generateToken());
            // $em = $this->getDoctrine()->getManager();
            // $em->persist($user);
            // $em->flush();

            $entityManager->persist($user);
            $entityManager->flush();

            // $this->mailer->sendEmail($user->getEmail(), $user->getToken());
            // $this->mailer->sendEmail($user->getEmail());

            $this->addFlash( type:"success", message:"Votre compte a bien été créé ! Veuillez valider le mail d'activation");
            // do anything else you need here, like send an email


            return $this->redirectToRoute('app_login');     

            // return $userAuthenticator->authenticateUser($user, $authenticator,$request);
        } 
        else{
            $this->addFlash( 'Erreur', 'Aucun compte n a été créé !');
        }

        return $this->render('registration/register.html.twig', 
       
        ['registrationForm' => $form->createView(), ]
    );
    }
    
/**
     * @Route("/confirmer-mon-compte/{token}", name="confirm_account")
     * @param string $token
     */
    #[Route('/confirmer-mon-compte/{token}', name: 'confirm_account', methods: ['GET','POST'])]
    public function confirmAccount(string $token, EntityManagerInterface $entityManager)
    {
        $user = $this->userRepository->findOneBy(["token" => $token]);
        //Vérifier si le user existe mais n'a pas encore activé son compte
        // if($user && !$user->getIsVerified()) {
        if($user) {
            $user->setToken(null);
            $user->setIsVerified(true);
            // $em = $this->getDoctrine()->getManager();
            // $em->persist($user);
            // $em->flush();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash("success", "Compte actif !");
            return $this->redirectToRoute('app_index');
        } else {
            $this->addFlash("error", "Ce compte n'exsite pas !");
            return $this->redirectToRoute('app_index');
        }
    }
    
    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');//on nettoie les valeurs encodés on retirant les +,/ et =
    }
}
