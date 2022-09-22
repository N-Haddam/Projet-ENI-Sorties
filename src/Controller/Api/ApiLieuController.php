<?php

namespace App\Controller\Api;


use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/lieu', name: 'api_lieu_')]
class ApiLieuController extends AbstractController
{

    #[Route('/liste/{idVille}', name: 'liste_par_ville', methods: ['GET'])]
    public function listeParVille(
        int $idVille,
        VilleRepository $villeRepository,
        LieuRepository $lieuRepository,
    ): Response {
        $ville = $villeRepository->find($idVille);
        $lieux = $lieuRepository->getLieuxParVille($ville);
        return $this->json(
            $lieux,
            Response::HTTP_OK,
            [],
            ['groups' => 'liste_lieux_par_ville']);
    }
}