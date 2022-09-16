<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\isEmpty;

class MainController extends AbstractController
{

    public EntityManagerInterface $entityManager;
    private Response $response;

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
        Request $request,
        SortieRepository $sortieRepository,
        CampusRepository $campusRepository,
    ): Response
    {
        $listeCampus = $campusRepository->findAll();
        $user = $this->getUser();
        $campus = $user->getCampus();
        $listeSorties = $sortieRepository->findBy(['siteOrganisateur'=>$campus]);
        $parametrageTwig = [];
        $parametrageTwig['methode'] = strtolower($request->getMethod());

        if (isset($_POST['campus']) && $_POST['campus'] !== $campus->getNom() ) {
            $campus = $campusRepository->findBy(['nom' => $_POST['campus']]);
            $listeSorties = $sortieRepository->findBy(['siteOrganisateur'=>$campus]);
        }
        $parametrageTwig['campusAAfficher'] = $campus;

        if (isset($_POST['nomSortieContient']) && $_POST['nomSortieContient'] !== ''){
            $portion = $_POST['nomSortieContient'];
            $listeSorties = $this->trierSortiesParPortion($listeSorties, $portion);
            $parametrageTwig['portion'] = $portion;
        }
        // TODO passer de datetime a date dans le twig
        if (isset($_POST['dateMin']) && isset($_POST['dateMax'])) {
            $listeSorties = $this->trierSortiesParDate($listeSorties, $_POST['dateMin'], $_POST['dateMax']);
            if ($_POST['dateMin'] !== '') {
                $parametrageTwig['dateMin'] = $_POST['dateMin'];
            }
            if ($_POST['dateMax'] !== '') {
                $parametrageTwig['dateMax'] = $_POST['dateMax'];
            }
        }

        if (!isset($_POST['organisateurTrue'])
            && !isset($_POST['inscritTrue'])
            && !isset($_POST['inscritFalse'])
            && !isset($_POST['sortiesPassees']))
        {
            if ($request->getMethod() !== 'POST') {
                $listeTmp = [];
                foreach ($listeSorties as $sortie) {
                    if ($sortie->getDateHeureDebut() >= new \DateTime()) {
                        $listeTmp[] = $sortie;
                    }
                }
                $listeSorties = $listeTmp;
                $parametrageTwig['ck'] = [];
            }
        } else {
            $listeSortiesTriCkBox = [];
            if (isset($_POST['organisateurTrue'])) {
                foreach ($listeSorties as $sortie) {
                    if ($sortie->getOrganisateur()->getUserIdentifier() === $user->getUserIdentifier()) {
                        $listeSortiesTriCkBox[] = $sortie;
                    }
                }
                $parametrageTwig['ck']['organisateurTrue'] = true;
            }
            if (isset($_POST['inscritTrue'])) { // TODO à tester avec plus de fixtures et revoir le lien entre sortie et participants
                foreach ($listeSorties as $sortie) {
                    if ($sortie->getParticipants()) {
                        foreach ($sortie->getParticipants() as $participant) {
                            if (($participant->getUserIdentifier() === $user->getUserIdentifier())
                                && !in_array($sortie, $listeSortiesTriCkBox))
                            {
                                $listeSortiesTriCkBox[] = $sortie;
                            }
                        }
                    }
                }
                $parametrageTwig['ck']['inscritTrue'] = true;
            }
            if (isset($_POST['inscritFalse'])) { // TODO à tester avec plus de fixtures et revoir le lien entre sortie et participants
                foreach ($listeSorties as $sortie) {
                    if (!$sortie->getParticipants()) { // TODO à revoir si modification nullable (avec ajout automatique du créateur
                        $listeSortiesTriCkBox[] = $sortie;
                    } else {
                        $test = false;
                        foreach ($sortie->getParticipants() as $participant) {
                            if ($participant->getUserIdentifier() === $user->getUserIdentifier()) {
                                $test = true;
                                break;
                            }
                        }
                        if (!$test && !in_array($sortie, $listeSortiesTriCkBox)) {
                            $listeSortiesTriCkBox[] = $sortie;
                        }
                    }
                }
                $parametrageTwig['ck']['inscritFalse'] = true;
            }
            if (isset($_POST['sortiesPassees'])) { // TODO à tester avec plus de fixtures
                foreach ($listeSorties as $sortie) {
                    if ($sortie->getDateHeureDebut() <= new \DateTime() && !in_array($sortie, $listeSortiesTriCkBox)) {
                        $listeSortiesTriCkBox[] = $sortie;
                    }
                }
                $parametrageTwig['ck']['sortiesPassees'] = true;
            } else {
                $listeTmp = [];
                foreach ($listeSortiesTriCkBox as $sortie) {
                    if ($sortie->getDateHeureDebut() >= new \DateTime()) {
                        $listeTmp[] = $sortie;
                    }
                }
                $listeSortiesTriCkBox = $listeTmp;
            }
            $listeSorties = $listeSortiesTriCkBox;
        }

        usort($listeSorties, fn($a, $b) => ($a->getDateLimiteInscription() >= $b->getDateLimiteInscription()));

        return $this->render('main/index.html.twig', [
            "sorties" => $listeSorties,
            "listeCampus" => $listeCampus,
            "params" => $parametrageTwig,
        ]);
    }

    public function trierSortiesParDate(array $listeSorties, string $dateMin, string $dateMax): array {
        $nouvelleListe = [];
        if ($dateMin !== '' && $dateMax !== '') {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getDateHeureDebut() >= date_timestamp_set(new \DateTime(), strtotime($dateMin))
                    && $sortie->getDateHeureDebut() <= date_timestamp_set(new \DateTime(), strtotime($dateMax))) {
                    $nouvelleListe[] = $sortie;
                }
            }
        } elseif ($dateMin !== '') {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getDateHeureDebut() >= date_timestamp_set(new \DateTime(), strtotime($dateMin))) {
                    $nouvelleListe[] = $sortie;
                }
            }
        } elseif ($dateMax !== '') {
            foreach ($listeSorties as $sortie) {
                if ($sortie->getDateHeureDebut() <= date_timestamp_set(new \DateTime(), strtotime($dateMax))) {
                    $nouvelleListe[] = $sortie;
                }
            }
        } else {
            $nouvelleListe = $listeSorties;
        }
        return $nouvelleListe;
    }

    public function trierSortiesParPortion(array $listeSorties, string $portion): array {
        $nouvelleListe = [];
        foreach ($listeSorties as $sortie) {
            if (str_contains(strtolower($sortie->getNom()), strtolower($portion))) {
                $nouvelleListe[] = $sortie;
            }
        }
        return $nouvelleListe;
    }
}
