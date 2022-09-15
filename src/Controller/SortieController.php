<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use mysql_xdevapi\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie/{i}', name: 'detail_sortie', methods: ['GET', 'POST'])] // TODO valider que i soit un entier
    public function detail(
        int $i,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        Request $request,
    ): Response
    {
        $sortieDetails =  $sortieRepository->findDetailsSortie($i);

        $sortie = $sortieRepository->find($i);

        $nbPlaces = is_integer($sortie->getNbInscriptionMax());
        $nbParticipants =is_integer($sortie->getParticipants());
        $nbPlaces = $nbPlaces - $nbParticipants;
//        dd($sortieDetails);
        if (!$sortie) {
            throw $this->createNotFoundException();
        }

        if ($request->isMethod('POST')){
            $user= $this->getUser();
            if($sortie->getDateLimiteInscription() >= new \DateTime('now') && ($nbParticipants < $nbPlaces) ){
                $sortie ->addParticipant($user);
                $entityManager->persist($sortie);
                $entityManager->flush();
            } else {
                $this->addFlash('warning','Vous ne pouvez plus vous inscrire :(');
                return $this->redirectToRoute('detail_sortie',['i' => $sortie->getId()]);
            }

        }

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'sortieDetails' => $sortieDetails,
            'nbPlaces' => $nbPlaces
        ]);

    }

//    public  function sinscrire():Response
//    {
//
//    }
}
