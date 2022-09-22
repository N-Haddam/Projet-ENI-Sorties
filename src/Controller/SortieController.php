<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\EventListener\Archivage;
use App\EventListener\DatabaseActivitySubscriber;
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
        EntityManagerInterface $entityManager,
//        Archivage $archivage,
        DatabaseActivitySubscriber $activitySubscriber
    ): Response
    {
        if (!$this->getUser()->isActif()) {
            $this->addFlash('danger', 'Vous devez avoir le statut actif pour créer une sortie');
            return $this->redirectToRoute('app_main');
        }

        $villes = $villeRepository->findAll(); // TODO revoir par rapport à ce que disait Philippe surles findAll
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setSiteOrganisateur($campusRepository->findOneBy(['nom' => $_POST['campus']]))
                ->setLieu($lieuRepository->find($_POST['lieu']))
                ->setOrganisateur($this->getUser());
            if ($form->getClickedButton() && 'enregistrer' === $form->getClickedButton()->getName()) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Créée']));
            } elseif ($form->getClickedButton() && 'publier' === $form->getClickedButton()->getName()) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
            }
            $sortieRepository->add($sortie, true);
            $this->ajoutParticipant($sortie, $entityManager);
            $this->addFlash('success', 'La nouvelle sortie a bien été enregistrée');
            return $this->redirectToRoute('app_main');
        }

        return $this->renderForm('sortie/gerer.html.twig', [
            'form' => $form,
            'villes' => $villes,
        ]);
    }

    #[Route('/detail/{i}', name: 'detail', requirements: ['i' => '\d+'], methods: ['GET'])]
    public function detail(
        int $i,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        Request $request,
    ): Response
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->findDetailsSortie($i)[0]; // TODO faire que la requête ne renvoie qu'un résultat et pas un tableau

        if (!$sortie || ($user->getId() != $sortie->getOrganisateur()->getId() && $sortie->getEtat()->getLibelle() === 'Créée' )) {
            throw $this->createNotFoundException();
        }

        $nbPlaces = $this->nbPlaces($sortie);
        $userParticipe = $this->userParticipe($user, $sortie);

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'nbPlaces' => $nbPlaces,
            'userParticipe' => $userParticipe
        ]);
    }

    #[Route('/inscription/{i}', name: 'sinscrire', requirements: ['i' => '\d+'], methods: ['GET', 'POST'])]
    public function inscription(
        int $i,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
    ): Response
    {
        $sortie = $sortieRepository->find($i);

        if (!$sortie) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        if (!$user->isActif()) {
            $this->addFlash('danger', 'Vous devez avoir le statut actif pour vous inscrire à une sortie');
            return $this->redirectToRoute('app_main');
        }

        $sortieDetails = $sortieRepository->findDetailsSortie($i);
        $nbPlaces = $this->nbPlaces($sortie);
        $nbParticipants = is_integer($sortie->getParticipants());
        $userParticipe = $this->userParticipe($user, $sortie);

        if ($sortie->getDateLimiteInscription() >= new \DateTime('now')
            && ($nbParticipants < $nbPlaces)
            && $sortie->getEtat()->getId() !== 6)
        {
            if ($nbParticipants+1 === $nbPlaces) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Clôturée']));
            }
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
    }

    #[Route('/desinscription/{i}', name: 'desinscription', requirements: ['i' => '\d+'], methods: ['GET', 'POST'])]
    public function desinscription(
        int $i,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository
    ): Response
    {
        $sortie = $sortieRepository->find($i);

        if (!$sortie) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        $nbPlaces = $this->nbPlaces($sortie);
        $sortieDetails = $sortieRepository->findDetailsSortie($i);
        $userParticipe = $this->userParticipe($user, $sortie);

        if ($sortie->getDateHeureDebut() >= new \DateTime('now')) {
//            if ($sortie->getEtat()->getLibelle() === 'Clôturée') // TODO s'il n'y avait plus de place et que donc la sortie était cloturée, il faut la rouvrir
            if ($nbPlaces === 0 && $sortie->getEtat()->getLibelle() === 'Annulée' && $sortie->getDateLimiteInscription() > new \DateTime()) {
                $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));
            }
            $sortie->removeParticipant($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $nbPlaces -= 1;
            $this->addFlash('success', 'Vous êtes désinscrit de cette sortie !');
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
    }

    #[Route('/publier/{i}', name: 'publier', methods: ['GET'])]
    public function publier(
        int $i,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository
    ): Response
    {
        $sortie = $sortieRepository->find($i);

        if (!$sortie) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        if (!$user->isActif()) {
            $this->addFlash('danger', 'Vous devez avoir le statut actif pour créer une sortie');
            return $this->redirectToRoute('app_main');
        }

        if ($sortie->getOrganisateur()->getUserIdentifier() === $user->getUserIdentifier()
            && $sortie->getEtat()->getLibelle() === 'Créée'
            && $sortie->getDateLimiteInscription() > new \DateTime())
        {
            $etatPubliee = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
            $sortie->setEtat($etatPubliee);
            $sortieRepository->add($sortie, true);
            $this->addFlash('success', 'Votre sortie est bien publiée');
            return $this->redirectToRoute('app_sortie_detail', ['i' => $sortie->getId()]);
        } else {
            $this->addFlash('warning', 'La sortie ne peut être publiée');
            return $this->redirectToRoute('app_main');
        }
    }

    #[Route('/annuler/{i}', name: 'annuler', methods: ['GET', 'POST'])]
    public function annuler(
        int $i,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository
    ): Response
    {
        $sortie = $sortieRepository->find($i);

        if (!$sortie) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        if (!$user->isActif()) {
            $this->addFlash('danger', 'Vous devez avoir le statut actif pour annuler une sortie');
            return $this->redirectToRoute('app_main');
        }

        if (isset($_POST['motif'])) {
            // check motif not null
            if ($_POST['motif'] === '') {
                $this->addFlash('danger', 'Vous devez renseigner un motif d\'annulation');
                return $this->redirectToRoute('app_sortie_annuler', ['i' => $i]);
            }
            if (strlen($_POST['motif']) > 255) {
                $this->addFlash('danger', 'Le motif ne peut dépasser 255 caractères !');
                return $this->redirectToRoute('app_sortie_annuler', ['i' => $i]);
            }
            if ($sortie->getOrganisateur()->getId() === $user->getId()
                && ($sortie->getEtat()->getId() === 1
                    || $sortie->getEtat()->getId() === 2))
            {
                $etatAnnule = $etatRepository->find(6);
                $sortie->setMotifAnnulation($_POST['motif']);
                $sortie->setEtat($etatAnnule);
                $sortieRepository->add($sortie, true);
                $this->addFlash('success', 'La sortie a bien été annulée');
                return $this->redirectToRoute('app_sortie_detail', ['i' => $sortie->getId()]);
            } else {
                $this->addFlash('warning', 'La sortie ne peut être annulée');
                return $this->redirectToRoute('app_main');
            }
        }
        return $this->render('sortie/annuler.html.twig', [
            "sortie" => $sortie,
        ]);
    }

    #[Route('/modifier/{i}', name: 'modifier', methods: ['GET', 'POST'])]
    public function modifier(
        int $i,
        SortieRepository $sortieRepository,
        VilleRepository $villeRepository,
        Request $request,
        CampusRepository $campusRepository,
        LieuRepository $lieuRepository,
        EtatRepository $etatRepository,
    ): Response
    {
        $sortie = $sortieRepository->find($i);

        if (!$sortie) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();

        if (!$user->isActif()) {
            $this->addFlash('danger', 'Vous devez avoir le statut actif pour modifier une sortie');
            return $this->redirectToRoute('app_main');
        }

        if ($user->getUserIdentifier() !== $sortie->getOrganisateur()->getUserIdentifier()) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier cette sortie !');
            return $this->redirectToRoute('app_sortie_detail', ['i' => $i]);
        }

        if ($sortie->getEtat()->getLibelle() !== 'Créée') {
            $this->addFlash('danger', 'Cette sortie n\'est plus modifiable !');
            return $this->redirectToRoute('app_sortie_detail', ['i' => $i]);
        }

        $villes = $villeRepository->findAll(); // TODO revoir pour afficher dans un ordre précis
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setSiteOrganisateur($campusRepository->find($_POST['campus']))
                ->setLieu($lieuRepository->find($_POST['lieu']));
            if ($form->getClickedButton() && 'enregistrer' === $form->getClickedButton()->getName()) {
                $sortie->setEtat($etatRepository->find(1));
            } elseif ($form->getClickedButton() && 'publier' === $form->getClickedButton()->getName()) {
                $sortie->setEtat($etatRepository->find(2));
            }
            $sortieRepository->add($sortie, true);
            $this->addFlash('success', 'La sortie a bien été modfiée');
            return $this->redirectToRoute('app_sortie_detail', ['i' => $sortie->getId()]);
        }

        $listCampus = $campusRepository->findAll();

        return $this->renderForm('sortie/gerer.html.twig', [
            'form' => $form,
            'villes' => $villes,
            'sortie' => $sortie,
            'listeCampus' => $listCampus,
        ]);

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
