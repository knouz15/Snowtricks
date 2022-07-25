<?php

namespace App\Controller;

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
// use App\Entity\Comment;
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
    #[Route('/trick/creation', 'trick_new', methods: ['GET', 'POST'])]
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
                'Votre trick a été créé avec succès !'
            );

            return $this->redirectToRoute('trick_show',['id'=>$trick->getId()]);
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
    #[Route('/trick/{id}', name: 'trick_show', methods: ['GET'])]
    public function show(
        Trick $trick

    ): Response { //le repository sert à gérer la récupération des données
        

        return $this->render('trick/show.html.twig', ['trick' => $trick]);
    }
}
