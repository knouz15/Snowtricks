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

class UserController extends AbstractController
{
    
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

    //             return $this->redirectToRoute('trick.index');
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

                return $this->redirectToRoute('trick.index');
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
