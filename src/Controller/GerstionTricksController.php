<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\TrickRepository;
// use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Bundle\FrameworkBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * This controller display all tricks
     *
     * @param TrickRepository $repository
     * @param Request $request
     * @return Response
     */
    #[Route('/mesTricks', name: 'trick.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        TrickRepository $repository,
        Request $request
    ): Response {
        $tricks = $repository->findBy(['user' => $this->getUser()]);

        return $this->render('pages/trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }



    /**
     * This controller show a form which create an trick
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/trick/creation', 'trick.new',methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
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
            
            // dd($manager);
            $trick->setUser($this->getUser());
            // $entityManager = $doctrine->getManager();
            $manager->persist($trick);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre trick a été créé avec succès !'
            );

            return $this->redirectToRoute('trick.index');
        }

        return $this->render('pages/trick/new.html.twig', 
        ['form' => $form->createView(),]
        );
    }

    /** controlleur show
    * @param $name
    * @param ManagerRegistry $doctrine
    * @param Request $request
    * @return Response
    */ 
    #[Route('/trick/{name}', name: 'trick.show', methods:['GET', 'POST'])]
    public function show($name, ManagerRegistry $doctrine, 
    TrickRepository $trickRepo, Request $request,
    
    ): Response
    {//le repository sert à gérer la récupération des données
        $trick = $trickRepo->findOneBy(['name' => $name ]);
        //dd($trick);
    
           

        return $this->render('pages/trick/show.html.twig', ['trick' => $trick]);
    }
}
    






