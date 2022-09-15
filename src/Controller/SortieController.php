<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'app_sortie_')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        Request $request,
        VilleRepository $villeRepository,
        LieuRepository $lieuRepository,
        EtatRepository $etatRepository,
        SortieRepository $sortieRepository,
        CampusRepository $campusRepository
    ): Response
    {
        $villes = $villeRepository->findAll(); // TODO revoir par rapport à ce que disait Philippe surles findAll
//        dd($villes);
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat($etatRepository->find(1)) // TODO : à gérer selon le bouton cliquer pour le formulaire
                ->setSiteOrganisateur($campusRepository->findOneBy(['nom' => $_POST['campus']]))
                ->setLieu($lieuRepository->find($_POST['lieu']))
                ->setOrganisateur($this->getUser());
            $sortieRepository->add($sortie, true);
            $this->addFlash('success', 'La nouvelle sortie a bien été enregistrée');
            return $this->redirectToRoute('app_main');
        }

        return $this->renderForm('sortie/create.html.twig', [
            'form' => $form,
            'villes' => $villes,
//            'lieux' => $lieuRepository->findBy(['ville' => $villes[0]])
        ]);
    }
    #[Route('/detail/{i}', name: 'detail', methods: ['GET', 'POST'])] // TODO valider que i soit un entier
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
}
