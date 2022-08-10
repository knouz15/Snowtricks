<?php

namespace App\Controller;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Video;
use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Entity\Comment;
use App\Form\CommentType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
// use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Bundle\FrameworkBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// #[Route('/crud')]
class TrickController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(
        TrickRepository $trickRepository
    ): Response {
        $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC'], 5);
        // $tricks = $trickRepository->findAll();
        $trickCount = $trickRepository->count([]);
        return $this->render('trick/index.html.twig', [
            // 'tricks' => $trickRepository->findAll()
            'tricks' => $tricks,
            'trickCount' => $trickCount
        ]);
    }

    /**
     * This controller show a form which create an trick
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/Trick/creation', 'trick_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        SluggerInterface $slugger
    ): Response {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        // dd($trick);
        if ($form->isSubmitted() && $form->isValid()) {

            // $trick = $form->getData();
            $trick->setUser($this->getUser());
            //on récupère les images transmises
            /** @var UploadedFile $images */
            $images = $form->get('imagesFile')->getData();

            // this condition is needed because the 'images' field is not required
            // so the PDF file must be processed only when a file is uploaded
            // On boucle sur les images
            foreach ($images as $image) {
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $extension = $image->guessExtension();
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' .$extension;

                    // Move the file to the directory where images are stored
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    // On stocke l'image dans la base de données (son nom) pcq l'image est sur le disque
                    $img = new Image();
                    $img->setName($originalFilename . '.' . $extension); //on stoque le nom, $fichier est une string
                    $img->setPath($newFilename);
                    // updates the 'addImage' property to store the PDF file name
                    // instead of its contents
                    $trick->addImage($img);
                    // dd($trick);
                }
            }

            // dd($this->getUser());
            $manager->persist($trick);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre trick a été créé avec succès !'
            );

            return $this->redirectToRoute('trick_show',[
                'id' => $trick->getId(),
                'slug'=>$trick->getSlug()]);
        }

        return $this->render(
            'trick/new.html.twig',
            ['form' => $form->createView(),]
        );
    }

    /** controlleur show
     * @param $name
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/Trick/{id}-{slug}', name: 'trick_show', methods: ['GET','POST'])]
    public function show( $id,
        // Trick $trick,
        Request $request,
        ManagerRegistry $doctrine,
        CommentRepository $commentRepository,
        TrickRepository $trickRepository,

    )
    // : Response 
    { //le repository sert à gérer la récupération des données
        

        $trick = $trickRepository->findOneBy(['id' => $id]);

        $trickid = $trick->getId();


        $comments = $commentRepository->findByTrick($trickid, ['createdAt' => 'DESC'], 5, 0);

        // $commentCount = $commentRepository->count([]);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();



        return $this->render('trick/trick_show.html.twig', [
            'slug' => $trick->getSlug(),
            'trick' => $trick,
            'user' => $user,
            'comments' => $comments
            // 'commentCount' => $commentCount
        ]);
        

        ///////
        // // On crée le commentaire "vierge"
        // $comment = new Comment;

        // // On génère le formulaire
        // $commentForm = $this->createForm(CommentType::class, $comment);

        // $commentForm->handleRequest($request);

        // // Traitement du formulaire
        // if($commentForm->isSubmitted() && $commentForm->isValid()){
        //     $comment->setCreatedAt(new \DateTimeImmutable());
        //     $comment->setTrick($trick);
        //     // $trick = $form->getData();
        //     $comment->setUser($this->getUser());


        //     // On va chercher le commentaire correspondant
        //     $em = $doctrine->getManager();


        //     $em->persist($comment);
        //     $em->flush();

        //     $this->addFlash('message', 'Votre commentaire a bien été envoyé');
        //     return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        // }

        // return $this->render('trick/show.html.twig', [
        //     'trick' => $trick,
        //     'user' => $user,
        //     'comments' => $comments
        //     // 'commentForm' => $commentForm->createView(),
        // //     'comments' => $paginator,
        // //     'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
        // //     'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        // ]);
    }

    // #[Security("is_granted('ROLE_USER')]
    #[Route('/Trick/edition/{slug}', name: 'trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Trick $trick, // grace au paramConverter
        Request $request,
        // TrickRepository $trickRepository,
        EntityManagerInterface $manager,
        SluggerInterface $slugger
        ): Response
    {
        // if(!$this->getUser()){
        //     return $this->redirectToRoute('app_index');
        // }
        // $trick = $trickRepository->findOneBy(['slug'=>$slug]);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            //on récupère les images transmises
            /** @var UploadedFile $images */
            $images = $form->get('imagesFile')->getData();
            // On boucle sur les images
            foreach ($images as $image) {
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $extension = $image->guessExtension();
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

                    // Move the file to the directory where images are stored
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    // On stocke l'image dans la base de données (son nom) pcq l'image est sur le disque
                    $img = new Image();
                    $img->setName($originalFilename . '.' . $extension); //on stoque le nom, $fichier est une string
                    $img->setPath($newFilename);
                    // updates the 'addImage' property to store the PDF file name
                    // instead of its contents
                    $trick->addImage($img);
                    // dd($trick);
                }
            }
            $trick->setUpdatedAt(new \DateTimeImmutable);
            $manager->persist($trick);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre trick a été modifié avec succès !'
            );

            return $this->redirectToRoute('trick_show',
            ['slug'=>$trick->getSlug()]);
        }
 
        return $this->render(
            'trick/edit.html.twig',[
            'trick' => $trick,
            'form' => $form->createView()]
        );
    }

    #[Route('/Trick/suppression/{slug}', 'trick_delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
    public function delete_trick(Request $request,
    ManagerRegistry $doctrine,
        // EntityManagerInterface $manager,
        Trick $trick
    ): Response {
        //$doctrine->getManager()->remove($trick);
        //$doctrine->getManager()->flush();

        // if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre trick a été supprimé avec succès !'
        );
    // }
        return $this->redirectToRoute('app_index');
    }


    #[Route('/supprime/image/{id}', name:'trick_delete_image', methods:['GET'])]
    public function delete_Image(Image $image, Request $request, ManagerRegistry $doctrine)
    {
       
          // On récupère le nom de l'image
            $nom = $image->getPath();
            // On supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $doctrine->getManager();
            //$em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            
            return $this->redirectToRoute('trick_edit',
            ['slug'=>$image->getTrick()->getSlug()]);
    }

    #[Route('/supprime/video/{id}', name:'trick_delete_video', methods:['GET'])]
    public function delete_Video(Video $video, Request $request, ManagerRegistry $doctrine)
    {
            // On supprime l'entrée de la base
            $em = $doctrine->getManager();
            //$em = $this->getDoctrine()->getManager();
            $em->remove($video);
            $em->flush();

            return $this->redirectToRoute('trick_edit',
            ['slug'=>$video->getTrick()->getSlug()]);
    }


    /**
     * @Route("/load-more/{start}",name="load_more")
     */
    #[Route('/load-more/{start}', name: 'load_more')]
    public function load_more(Request $request, TrickRepository $trickRepository, $start = 15)
    {
        if ($request->isXmlHttpRequest()) {

            $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC'], 5, $start);

            return $this->render('trick/list_tricks.html.twig', [
                'tricks' => $tricks
            ]);
        }
    }

    
    #[Route('/comment_add/{id}', name: 'comment_add')]
    public function addComment(
        Request $request, 
        Trick $trick, 
        EntityManagerInterface $em, 
        // ManagerRegistry $doctrine,
        )
    {
       

            // /**@var \App\Entity\User $user */
            // $user = $this->getUser();
            // $comment->setUser($user);
            // $comment->setContent($request->request->get('comment'));
            // $comment->setTrick($trick);
            // $comment->setCreatedAt(new \DateTimeImmutable);

            // $em->persist($comment);
            // $em->flush();

            // /** @var FlashBag */
            // $flashBag = $session->getBag('flashes');

            // $flashBag->add('success', "Le commentaire a bien été ajouté !");


            // On crée le commentaire "vierge"
        $comment = new Comment;

        // On génère le formulaire
        // $commentForm = $this->createForm(CommentType::class, $comment);

        // $commentForm->handleRequest($request);

        // Traitement du formulaire
        // if($commentForm->isSubmitted() && $commentForm->isValid()){
            // $comment->setCreatedAt(new \DateTimeImmutable());
            // $comment->setTrick($trick);
            // // $trick = $form->getData();
            // $comment->setContent($request->request->get('comment'));

            // $comment->setUser($this->getUser());
            // On va chercher le commentaire correspondant
            // $em = $doctrine->getManager();
 /**@var \App\Entity\User $user */
 $user = $this->getUser();
 $comment->setUser($user);
 $comment->setContent($request->request->get('comment'));
 $comment->setTrick($trick);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();
            $this->addFlash( type:"success", message:"Votre commentaire a bien été envoyé");

            // $this->addFlash('message', 'Votre commentaire a bien été envoyé');
            // return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        // }

        // return $this->render('trick/show.html.twig', [
        //     'trick' => $trick,
        //     // 'user' => $user,
        //     // 'comments' => $comments,
        //     'commentForm' => $commentForm->createView(),
        //     'comments' => $paginator,
        //     'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
        //     'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        // ]);



        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }


    
    #[Route('/load-more-comments/{id}/{start}', name: 'load_more_comments')]
    public function load_more_comments(Request $request, CommentRepository $commentRepository, $start = 5, $id =0)
    {       
        
            if ($request->isXmlHttpRequest()) {

            $comments = $commentRepository->findByTrick($id, ['createdAt' => 'DESC'], 5, $start);
            // dd($id);
            // $slug = $this->getTrick()->getSlug();
            return $this->render('trick/comments-list.html.twig', [
                'comments' => $comments
            ]);
        }
    }
}
