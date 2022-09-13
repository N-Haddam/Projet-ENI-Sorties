<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_default', methods: ['GET'])]
    public function default(): Response {
        return $this->redirectToRoute('app_main');
    }

    #[Route('/main', name: 'app_main', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', []);
    }

    #[Route('/main', name: 'app_main', methods: ['GET', 'POST'])]
    public function list(
        SortieRepository $sortieRepository,
        CampusRepository $campusRepository,
    ): Response
    {
        $campus = $campusRepository->findAll();

        $sorties =$sortieRepository->findAll();

        return $this->render('main/index.html.twig', [
            "sorties" =>$sorties,
            "campus" =>$campus
        ]);
    }

}
