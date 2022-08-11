<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\ResetService;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use App\Form\UserResetPasswordType;
use App\Form\UserForgotPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserController extends AbstractController
{
    
    #[Security("is_granted('ROLE_USER')")]
    #[Route('/Utilisateur/edition', 'user_edit_profil', methods: ['GET', 'POST'])]
    public function  editProfile(
        Request $request,
        EntityManagerInterface $manager,
        SluggerInterface $slugger
        ):Response
    {

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData(); 
            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $avatarFile->guessExtension();
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;
                try {
                    $avatarFile->move(
                    $this->getParameter('avatars_directory'),
                    $newFilename
                    );
                    } catch (FileException $e) {
                }
                $user->setAvatarFilename($newFilename);
            }
        
            $manager->persist($user);
            
            $manager->flush();

            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('app_index');
        }
        return $this->render('security/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/passwordoublie', name:'forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $em,
        ResetService $mail
        
    ): Response
    {
        $form = $this->createForm(UserForgotPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $donnees = $form->getData();
            
            $user = $userRepository->findOneByUsername( 
                $donnees['username']);

            if($user){
                
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $context = compact('url', 'user');
                $mail->sendEmail(
                    'no-reply@snowtricks.fr',
                    $user->getEmail(),
                    'Réinitialisation du mot de passe oublié',
                    'reset_password_reponse',
                    $context
                );

                $this->addFlash(type:"success", message:"Votre demande a bien été envoyée ! Veuillez valider le mail de réinitialisation");
                return $this->redirectToRoute('app_login');
            }
            else{
            throw new NotFoundHttpException("User invalide");
            }
        }
        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }   

    #[Route('/mot-de-passe-oublie/{token}', name:'reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    { 
        
        $user = $userRepository->findOneByResetToken($token);
        
        if($user){
            $form = $this->createForm(UserResetPasswordType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
        
        throw new NotFoundHttpException("Jeton invalide");
            
    }
}


