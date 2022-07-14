<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tag;
use App\Entity\Image;
use App\Entity\Trick;
// use App\Entity\Comment;
use App\Form\TrickType;
// use App\Form\CommentType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
// use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Bundle\FrameworkBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class TrickController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(
        TrickRepository $trickRepository
    ): Response
    {
        $tricks = $trickRepository->findBy([],['createdAt' => 'DESC'], 15);
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll()
        ]);
    }

    

    /**
     * This controller show a form which create an trick
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/trick/creation', 'trick_new',methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        // ManagerRegistry $doctrine
    ): Response 
    {
        
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // $entityManager = $doctrine->getManager();
            $trick = $form->getData();
            //  $trick->setUser($this->getUser());
            
            // dd($trick);
            $trick->setUser($this->getUser());
            // $entityManager = $doctrine->getManager();
            $manager->persist($trick);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre trick a été créé avec succès !'
            );

            return $this->redirectToRoute('app_index');
        }

        return $this->render('trick/new.html.twig', 
        ['form' => $form->createView(),]
        );
    }

    /** controlleur show
    * @param $name
    * @param ManagerRegistry $doctrine
    * @param Request $request
    * @return Response
    */ 
    #[Route('/trick/{name}', name: 'trick_show', methods:['GET', 'POST'])]
    public function show($name, ManagerRegistry $doctrine, 
    TrickRepository $trickRepo, Request $request,
    
    ): Response
    {//le repository sert à gérer la récupération des données
        $trick = $trickRepo->findOneBy(['name' => $name ]);

        return $this->render('trick/show.html.twig', ['trick' => $trick]);
    }
}
