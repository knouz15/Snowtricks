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
use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TrickController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(
        TrickRepository $trickRepository
    ): Response {
        $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC'], 5);
        $trickCount = $trickRepository->count([]);
        return $this->render('trick/index.html.twig', [
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
        $form = $this->createForm(TrickType::class, $trick,);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setUser($this->getUser());
            /** @var UploadedFile $images */
            $images = $form->get('imagesFile')->getData();

            foreach ($images as $image) {
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $image->guessExtension();
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' .$extension;

                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }
                    $img = new Image();
                    $img->setName($originalFilename . '.' . $extension); 
                    $img->setPath($newFilename);
                    $trick->addImage($img);
                }
            }

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
    #[Route('/Trick/{slug}', name: 'trick_show', methods: ['GET','POST'])]
    public function show( $slug,
        Request $request,
        ManagerRegistry $doctrine,
        CommentRepository $commentRepository,
        TrickRepository $trickRepository,

    )
    { 
        

        $trick = $trickRepository->findOneBy(['slug' => $slug]);

        $trickid = $trick->getId();


        $comments = $commentRepository->findByTrick($trickid, ['createdAt' => 'DESC'], 5, 0);


        /** @var \App\Entity\User $user */
        $user = $this->getUser();



        return $this->render('trick/trick_show.html.twig', [
            'slug' => $trick->getSlug(),
            'trick' => $trick,
            'user' => $user,
            'comments' => $comments
        ]);
        
    }

    // #[Security("is_granted('ROLE_USER')]
    #[Route('/Trick/edition/{slug}', name: 'trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Trick $trick, 
        Request $request,
        EntityManagerInterface $manager,
        SluggerInterface $slugger
        ): Response
    {
        
        $form = $this->createForm(TrickType::class, $trick);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            /** @var UploadedFile $images */
            $images = $form->get('imagesFile')->getData();
            foreach ($images as $image) {
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $image->guessExtension();
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $img = new Image();
                    $img->setName($originalFilename . '.' . $extension); 
                    $img->setPath($newFilename);
                    $trick->addImage($img);
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
    public function deleteTrick(Request $request,
    ManagerRegistry $doctrine,
        Trick $trick
    ): Response {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre trick a été supprimé avec succès !'
        );
        return $this->redirectToRoute('app_index');
    }


    #[Route('/supprime/image/{id}', name:'trick_delete_image', methods:['GET'])]
    public function deleteImage(Image $image, Request $request, ManagerRegistry $doctrine)
    {
       
            $nom = $image->getPath();
            unlink($this->getParameter('images_directory').'/'.$nom);

            $em = $doctrine->getManager();
            $em->remove($image);
            $em->flush();

            
            return $this->redirectToRoute('trick_edit',
            ['slug'=>$image->getTrick()->getSlug()]);
    }

    #[Route('/supprime/video/{id}', name:'trick_delete_video', methods:['GET'])]
    public function deleteVideo(Video $video, Request $request, ManagerRegistry $doctrine)
    {
            $em = $doctrine->getManager();
            $em->remove($video);
            $em->flush();

            return $this->redirectToRoute('trick_edit',
            ['slug'=>$video->getTrick()->getSlug()]);
    }


    
    #[Route('/load-more/{start}', name: 'load_more')]
    public function loadMore(Request $request, TrickRepository $trickRepository, $start = 15)
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
        )
    {
       
        $comment = new Comment;

    
        /**@var \App\Entity\User $user */
        $user = $this->getUser();
        $comment->setUser($user);
        $comment->setContent($request->request->get('comment'));
        $comment->setTrick($trick);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $em->persist($comment);
        $em->flush();
        $this->addFlash( type:"success", message:"Votre commentaire a bien été envoyé");

        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    #[Route('/load-more-comments/{id}/{start}', name: 'load_more_comments')]
    public function loadMoreComments(Request $request, CommentRepository $commentRepository, $start = 5, $id =0)
    {       
        
            if ($request->isXmlHttpRequest()) {

            $comments = $commentRepository->findByTrick($id, ['createdAt' => 'DESC'], 5, $start);
           
            return $this->render('trick/comments-list.html.twig', [
                'comments' => $comments
            ]);
        }
    }
}

