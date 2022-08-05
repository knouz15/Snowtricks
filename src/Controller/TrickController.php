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
        $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC'], 15);
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

            return $this->redirectToRoute('trick_show',['slug'=>$trick->getSlug()]);
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
    public function show(
        Trick $trick,
        Request $request,
        ManagerRegistry $doctrine,
        CommentRepository $commentRepository

    ): Response { //le repository sert à gérer la récupération des données
        
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($trick, $offset);

        // Partie commentaires
        // On crée le commentaire "vierge"
        $comment = new Comment;

        // On génère le formulaire
        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        // Traitement du formulaire
        if($commentForm->isSubmitted() && $commentForm->isValid()){
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setTrick($trick);
            // $trick = $form->getData();
            $comment->setUser($this->getUser());


            // On va chercher le commentaire correspondant
            $em = $doctrine->getManager();


            $em->persist($comment);
            $em->flush();

            $this->addFlash('message', 'Votre commentaire a bien été envoyé');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $commentForm->createView(),
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]);
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
        // $trick = $trickRepository->findOneBy(['id'=>$trick->getId()]);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
          
            //on récupère les images transmises
            /** @var UploadedFile $images */
            $images = $form->get('imagesFile')->getData();
            // dd($images);
            // this condition is needed because the 'images' field is not required
            // so the PDF file must be processed only when a file is uploaded
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

            // dd($this->getUser());
            $manager->persist($trick);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre trick a été modifié avec succès !'
            );

            return $this->redirectToRoute('trick_show',
            // ['id'=>$trick->getSlug()]);
            ['slug'=>$trick->getSlug()]);
        }
 
        return $this->render(
            'trick/edit.html.twig',[
            'trick' => $trick,
            'form' => $form->createView()]
        );
    }

    /**
     * This controller allows us to delete an trick
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param Trick $trick
     * @return Response
     */
    #[Route('/Trick/suppression/{slug}', 'trick_delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
    public function delete(Request $request,
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

/** 
    * @param Image $image
    * @param Request $request
    * @param ManagerRegistry $doctrine
    * @Route("/supprime/image/{id}", name="trick.delete.image", methods={"DELETE"})
    * @return Response

     
     */
    #[Route('/supprime/image/{id}', name:'trick_delete_image', methods:['GET'])]
    public function deleteImage(Image $image, Request $request, ManagerRegistry $doctrine)
    {
        $data = json_decode($request->getContent(), true);//on recupère les données qui ns seront transférées en json, on les décode

        // On vérifie si le token est valide
        // if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){//on sécurise le formulaire avec le token csrfToken pr éviter que cette route ne soit utilisée par n'importe qui
            // On récupère le nom de l'image
            $nom = $image->getName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $doctrine->getManager();
            //$em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        // }else{
        //     return new JsonResponse(['error' => 'Token Invalide'], 400);
        // }
    }

    /** 
    * @param Video $video
    * @param Request $request
    * @param ManagerRegistry $doctrine
    * @Route("/supprime/video/{id}", name="trick.delete.video", methods={"DELETE"})
    * @return Response

     
     */
    #[Route('/supprime/video/{id}', name:'trick_delete_video', methods:['DELETE'])]
    public function deleteVideo(Video $video, Request $request, ManagerRegistry $doctrine)
    {
        $data = json_decode($request->getContent(), true);//on recupère les données qui ns seront transférées en json, on les décode

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$video->getId(), $data['_token'])){//on sécurise le formulaire avec le token csrfToken pr éviter que cette route ne soit utilisée par n'importe qui
            // On récupère le nom de la vidéo
            $nom = $video->getUrl();
            // On supprime le fichier
            unlink($this->getParameter('videos_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $doctrine->getManager();
            //$em = $this->getDoctrine()->getManager();
            $em->remove($video);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

}
