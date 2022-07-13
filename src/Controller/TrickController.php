<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
