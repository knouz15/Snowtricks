<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;


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
    // #[Route('/utilisateur/mot-de-passe-oublie', 'user.forgot.password', methods: ['GET', 'POST'])]
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
    /**
     * This controller allow us to edit user's password
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(
        User $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                //$choosenUser->setUpdatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword(
                    $form->getData()['newPassword']
                );

                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié.'
                );

                $manager->persist($choosenUser);
                $manager->flush();

                return $this->redirectToRoute('app_index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('security/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
