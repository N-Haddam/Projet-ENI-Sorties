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
        CampusRepository $campusRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $villes = $villeRepository->findAll(); // TODO revoir par rapport à ce que disait Philippe surles findAll
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat($etatRepository->find(1)) // TODO : à gérer selon le bouton cliquer pour le formulaire
                ->setSiteOrganisateur($campusRepository->findOneBy(['nom' => $_POST['campus']]))
                ->setLieu($lieuRepository->find($_POST['lieu']))
                ->setOrganisateur($this->getUser());
            $sortieRepository->add($sortie, true);
            $this->ajoutParticipant($sortie, $entityManager);
            $this->addFlash('success', 'La nouvelle sortie a bien été enregistrée');
            return $this->redirectToRoute('app_main');
        }

        return $this->renderForm('sortie/create.html.twig', [
            'form' => $form,
            'villes' => $villes,
//            'lieux' => $lieuRepository->findBy(['ville' => $villes[0]])
        ]);
    }

    #[Route('/detail/{i}', name: 'detail', methods: ['GET'])] // TODO valider que i soit un entier
    public function detail(
        int $i,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        Request $request,
    ): Response
    {
        $sortieDetails = $sortieRepository->findDetailsSortie($i);
        $sortie = $sortieRepository->find($i);
        $nbPlaces = $this->nbPlaces($sortie);
        $user = $this->getUser();
        $userParticipe = $this->userParticipe($user, $sortie);

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'sortieDetails' => $sortieDetails,
            'nbPlaces' => $nbPlaces,
            'userParticipe' => $userParticipe
        ]);
    }

        #[Route('/inscription/{i}', name: 'sinscrire', methods: 'POST')] // TODO valider que i soit un entier
        public function inscription(
            int $i,
            EntityManagerInterface $entityManager,
            SortieRepository $sortieRepository,
        ): Response
        {

        $user = $this->getUser();
        $sortie = $sortieRepository->find($i);
        $sortieDetails = $sortieRepository->findDetailsSortie($i);
        $nbPlaces = $this->nbPlaces($sortie);
        $nbParticipants = is_integer($sortie->getParticipants());
        $userParticipe = $this->userParticipe($user, $sortie);

        if(isset($_POST['sinscrire'])) {

            if ($sortie->getDateLimiteInscription() >= new \DateTime('now') && ($nbParticipants < $nbPlaces)) {
                $sortie = $sortieRepository->find($i);
                $i = $sortie->getId();
                $this->ajoutParticipant($sortie, $entityManager);
                $this->addFlash('success', 'Vous êtes inscrit à cette sortie !');
                return $this->forward('App\Controller\SortieController::detail', [
                    'i' => $i,
                    'sortie' => $sortie,
                    'sortieDetails' => $sortieDetails,
                    'nbPlaces' => $nbPlaces,
                    'userParticipe' => $userParticipe
                ]);
            } else {
                $this->addFlash('warning', 'Vous ne pouvez plus vous inscrire :(');
                return $this->forward('App\Controller\SortieController::detail', [
                    'i' => $i,
                    'sortie' => $sortie,
                    'sortieDetails' => $sortieDetails,
                    'nbPlaces' => $nbPlaces,
                    'userParticipe' => $userParticipe
                ]);
            }
        }else {
            return $this->redirectToRoute('app_sortie_desinscription',['i' => $sortie->getId()]);
        }
    }

    #[Route('/desinscription/{i}', name: 'desinscription', methods: 'POST')] // TODO valider que i soit un entier
    public function desinscription(
        int $i,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
    ): Response
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->find($i);
        $nbPlaces = $this->nbPlaces($sortie);
        $sortieDetails = $sortieRepository->findDetailsSortie($i);
        $userParticipe = $this->userParticipe($user, $sortie);

        if(isset($_POST['desinscrire'])) {
            if ($sortie->getDateHeureDebut() >= new \DateTime('now')) {
                $sortie->removeParticipant($user);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('warning', 'Vous êtes désinscrit de cette sortie !');
                return $this->forward('App\Controller\SortieController::detail',  [
                    'i' => $sortie->getId(),
                    'sortie' => $sortie,
                    'sortieDetails' => $sortieDetails,
                    'nbPlaces' => $nbPlaces,
                    'userParticipe' => $userParticipe
                ]);
            }else {
                $this->addFlash('warning', 'Vous ne pouvez plus vous désinscrire :(');
                return $this->redirectToRoute('app_sortie_detail', ['i' => $sortie->getId()]);
            }
        }else {
            return $this->redirectToRoute('app_sortie_detail', ['i' => $sortie->getId()]);
        }
    }


 // ----------------------------------------------------------------------------------------------------------------------

    public function userParticipe($user, $sortie):bool {
        $participants = $sortie->getParticipants();
        if($participants->contains($user)){
        return true;
        }else {
            return false;
        }
    }

    public function nbPlaces($sortie){
        $nbPlacesInit = (int)$sortie->getNbInscriptionMax();
        $participants = $sortie->getParticipants();
        $nbParticipants = $participants->count();
        $nbPlaces = $nbPlacesInit - $nbParticipants;
        return $nbPlaces;
    }

    public function ajoutParticipant($sortie, $entityManager){
        $user = $this->getUser();
        $sortie->addParticipant($user);
        $entityManager->persist($sortie);
        $entityManager->flush();
    }
}
