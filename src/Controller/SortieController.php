<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie/{i}', name: 'detail_sortie', methods: 'GET')] // TODO valider que i soit un entier
    public function detail(
        int $i,
        SortieRepository $sortieRepository,
        Request $request,
    ): Response
    {
        $sortieDetails =  $sortieRepository->findDetailsSortie($i);
        $sortie = $sortieRepository->find($i);

        $nbPlaces = is_integer($sortie->getNbInscriptionMax());
        $nbParticipants =is_integer($sortie->getParticipants());
        $nbPlaces = $nbPlaces - $nbParticipants;
        dd($sortieDetails);
        if (!$sortie) {
            throw $this->createNotFoundException();
        }

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'sortieDetails' => $sortieDetails,
            'nbPlaces' => $nbPlaces
        ]);
    }
}
