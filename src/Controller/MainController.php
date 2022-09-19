<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Filtres\CheckBoxFiltre;
use App\Filtres\DateFiltre;
use App\Filtres\DefaultCheckBoxFiltre;
use App\Filtres\PortionFiltre;
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
        DateFiltre $dateFiltre,
        DefaultCheckBoxFiltre $defaultCheckBoxFiltre,
        PortionFiltre $portionFiltre,
        CheckBoxFiltre $checkBoxFiltre,
    ): Response
    {
        // si get, méthode du repository getDefaultSortie (ou

//        $listeCampus = $campusRepository->findAll();
//        $user = $this->getUser();
//        $campus = $user->getCampus();

        $user = $this->getUser();
        $userCampus = $user->getCampus();
        // TODO revoir après la gestion du paramétrage de Twig
        $parametrageTwig = [];
        $parametrageTwig['methode'] = strtolower($request->getMethod());

        if ($request->getMethod() === 'GET') {
            $params = [
                "user" => $user,
                "campus" => $userCampus,
                "nomSortieContient" => "",
                "dateMin" => "",
                "dateMax" => "",
                "organisateurTrue" => "on",
                "inscritTrue" => "on",
                "inscritFalse" => "on",
            ];
        } else {
            $params = $_POST;
            $params['campus'] = $campusRepository->findOneBy(['nom' => $params['campus']]);
            $params['user'] = $user;
        }
        $sorties = $sortieRepository->findByRequest($params);
        return $this->render('main/index.html.twig', [
            "sorties" => $sorties,
            "campusAAficher" => $params['campus'], // TODO dans paramétrage twig array (peut-être juste $params comme $paramtwig, à voir
            "listeCampus" => $campusRepository->findAll(),
            "params" => $parametrageTwig,
        ]);


//        if (isset($_POST['campus']) && $_POST['campus'] !== $campus->getNom() ) {
//            $campus = $campusRepository->findOneBy(['nom' => $_POST['campus']]);
//        }
//        $listeSorties = $sortieRepository->findByCampus($campus);
//        $parametrageTwig['campusAAfficher'] = $campus;
//
//        if (isset($_POST['nomSortieContient']) && $_POST['nomSortieContient'] !== ''){
//            $portion = $_POST['nomSortieContient'];
//            $listeSorties = $portionFiltre->trierSortiesParPortion($listeSorties, $portion);
//            $parametrageTwig['portion'] = $portion;
//        }
//        // TODO passer de datetime a date dans le twig
//        if (isset($_POST['dateMin']) && isset($_POST['dateMax'])) {
//            $listeSorties = $dateFiltre->trierSortiesParDate($listeSorties, $_POST['dateMin'], $_POST['dateMax']);
//            if ($_POST['dateMin'] !== '') {
//                $parametrageTwig['dateMin'] = $_POST['dateMin'];
//            }
//            if ($_POST['dateMax'] !== '') {
//                $parametrageTwig['dateMax'] = $_POST['dateMax'];
//            }
//        }
//
//        if (!isset($_POST['organisateurTrue'])
//            && !isset($_POST['inscritTrue'])
//            && !isset($_POST['inscritFalse'])
//            && !isset($_POST['sortiesPassees']))
//        {
//            if ($request->getMethod() !== 'POST') {
//                $listeSorties = $defaultCheckBoxFiltre->triCkbDefault($listeSorties);
//                $parametrageTwig['ck'] = [];
//            }
//        } else {
//            list($parametrageTwig, $listeSorties) = $checkBoxFiltre->triCk($listeSorties, $user, $parametrageTwig, $defaultCheckBoxFiltre);
//        }
//
//        usort($listeSorties, fn($a, $b) => ($a->getDateLimiteInscription() >= $b->getDateLimiteInscription()));
//
//        return $this->render('main/index.html.twig', [
//            "sorties" => $listeSorties,
//            "listeCampus" => $listeCampus,
//            "params" => $parametrageTwig,
//        ]);
    }

}
