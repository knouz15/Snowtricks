<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Service\ResetService;
use App\Repository\UserRepository;
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
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserController extends AbstractController
{
    
    // #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    // #[Route('/utilisateur', 'mon_profil', methods: ['GET', 'POST'])]
    // public function index()
    // {
    //     return $this->render('security/index.html.twig');
    // }

    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/Utilisateur/edition/{id}', 'user_edit_profil', methods: ['GET', 'POST'])]
    public function  editProfile(
        User $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        SluggerInterface $slugger
        ):Response
    {

        // $user = $this->getUser();
        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData(); 
            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatar')->getData();

 // this condition is needed because the 'avatar' field is not required
 // so the PDF file must be processed only when a file is uploaded
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $extension = $avatarFile->guessExtension();
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;

                // Move the file to the directory where avatars are stored
                try {
                    $avatarFile->move(
                    $this->getParameter('avatars_directory'),
                    $newFilename
                    );
                } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                }

                // updates the 'avatarFilename' property to store the PDF file name
                // instead of its contents
                $user->setAvatarFilename($newFilename);
                // dd($user);
                // $user->setAvatarFilename(
                //     new File($this->getParameter('avatars_directory').'/'.$product->getAvatarFilename())
                // );
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



    // #[Security("is_granted('ROLE_USER')")]
    // #[Route('/utilisateur/mot-de-passe-oublie', 'forgot_password')]
    // // , methods: ['GET', 'POST'])]
    // public function forgotPassword
    // (
    //     // User $choosenUser,
    //     Request $request,
    //     EntityManagerInterface $manager,
    //     UserPasswordHasherInterface $hasher
    // ): Response {
    //     $form = $this->createForm(UserForgotPasswordType::class);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
            
    //                 $form->getData()['username'];

    //             $this->addFlash(
    //                 'success',
    //                 'Vous pouvez choisir un nouveau mot de passe.'
    //             );

    //             $manager->persist($user);
    //             $manager->flush();

    //             return $this->redirectToRoute('app_index');
    //         } else {
    //             $this->addFlash(
    //                 'warning',
    //                 'Le username renseigné est incorrect.'
    //             );
    //         }
        

    //     return $this->render('security/forgot_password.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }



    #[Route('/utilisateur/mot-de-passe-oublie', name:'forgot_password')]
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
            //On va chercher l'utilisateur par son email
            $user = $userRepository->findOneByUsername($form->get('username')->getData());

            // On vérifie si on a un utilisateur
            if($user){
                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);//important meme s'il retourne une erreur (visuelle seulement) car parfois il fait pas le lien repository entié
                //Rajouter catch/ try
                $em->persist($user);
                $em->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // On crée les données du mail
                $context = compact('url', 'user');// compact au lieu de faire un tableau context avec $user->$user et $url->$url

                // Envoi du mail
                $mail->send(
                    'no-reply@snowtricks.fr',//from
                    $user->getEmail(),//à
                    'Réinitialisation du mot de passe oublié',//titre
                    'reset_password_reponse',//le template
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            // $user est null
            $this->addFlash('danger', 'Saisie incorrecte');
            return $this->redirectToRoute('app_login');
        }
// dd($form);
        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }   

    #[Route('/utilisateur/mot-de-passe-oublie/{token}', name:'reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        // On vérifie si on a ce token dans la base
        $user = $userRepository->findOneByResetToken($token);
        
        if($user){
            $form = $this->createForm(UserResetPasswordType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user->setResetToken('');// On efface le token
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
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
        //$this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }

    // /**
    //  * This controller allow us to edit user's password
    //  *
    //  * @param User $choosenUser
    //  * @param Request $request
    //  * @param EntityManagerInterface $manager
    //  * @param UserPasswordHasherInterface $hasher
    //  * @return Response
    //  */
    // // #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    // #[Route('/utilisateur/Reinitialition-mot-de-passe/{id}', 'user_reset_password', methods: ['GET', 'POST'])]
    // public function resetPassword(
    //     User $choosenUser,
    //     Request $request,
    //     EntityManagerInterface $manager,
    //     UserPasswordHasherInterface $hasher
    // ): Response {
    //     $form = $this->createForm(UserPasswordType::class);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
    //             $choosenUser->setUpdatedAt(new \DateTimeImmutable());
    //             $choosenUser->setPlainPassword(
    //                 $form->getData()['newPassword']
    //             );

    //             $this->addFlash(
    //                 'success',
    //                 'Le mot de passe a été modifié.'
    //             );

    //             $manager->persist($choosenUser);
    //             $manager->flush();

    //             return $this->redirectToRoute('app_index');
    //         } else {
    //             $this->addFlash(
    //                 'warning',
    //                 'Le mot de passe renseigné est incorrect.'
    //             );
    //         }
    //     }

    //     return $this->render('security/reset_password.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }
}
