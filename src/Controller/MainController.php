<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_default', methods: ['GET'])]
    public function default(): Response {
        return $this->redirectToRoute('app_main');
    }

    #[Route('/main', name: 'app_main', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', []);
    }

    #[Route('/main', name: 'app_main', methods: ['GET'])]
    public function list(
        Request $request,
        SortieRepository $sortieRepository
    ): Response
    {
        $campus = $this->getUser()->getCampus();
        $listeSorties = $sortieRepository->findBy(['siteOrganisateur'=>$campus]);

//        dd($request->query->get('nomSortieContient'));
        if ($request->query->get('nomSortieContient') !== null){
            $portion = $request->query->get('nomSortieContient');
            dd($portion);
        }

//        if ($request->isMethod('get'))
//        {

//
//
//            return $this->showAllSorties();
//        } else
//        {
//            if (!isEmpty($_POST['campus'])
//
//
//            ) {
//            $response = new Response($this->filtreCampus());
//
//            }
//            if (
//            isEmpty($_POST['campus'])
//            && isset($_POST['dateMin'])
//            && isset($_POST['dateMax'])
//
//            && !isset($_POST['organisateurTrue'])
//            && !isset($_POST['inscritTrue'])
//            && !isset($_POST['inscritFalse'])
//            && !isset($_POST['sortiesPassees'])
//            ) {
//                    $response = new Response($this->dateNotNull());
//            }
//        return new Response($this->response = $response);
//        }

        return $this->render('main/index.html.twig', [
            "sorties" => null,
            "campus" => $this->getCampus()
        ]);
    }

// ---------------------------------------------------------------------------------------------

    public function showAllSorties(): Response{
        $campus = $this->getCampus();

        $sorties = $this->entityManager
            ->getRepository(Sortie::class)
            ->findAll();

        return $this->render('main/index.html.twig', [
            "sorties" => $sorties,
            "campus" => $campus,
        ]);
    }

    public function dateNotNull(): Response{
        $campus = $this->getCampus();

        $dateMin = $_POST['dateMin'];
        $dateMax = $_POST['dateMax'];

        $sorties = $this->entityManager
            ->getRepository(Sortie::class)
            ->findDateFiltered($dateMin, $dateMax);

        return $this->render('main/index.html.twig', [
            "sorties" => $sorties,
            "campus" => $campus,
        ]);


    }

    public function filtreCampus(SortieRepository $sortieRepository): array{

//        dd('filtre campus');

        $campusChoisi = $_POST['campus'];
        $campusSelectionne = $this->entityManager
            ->getRepository(Campus::class)
            ->findOneBy(['nom' => $campusChoisi]);
        $campusId = $campusSelectionne->getId();

        $sorties = $this->entityManager
            ->getRepository(Sortie::class)
            ->findBy(['siteOrganisateur' => $campusId]);
        return $sorties;
    }

    public function getCampus(): array
    {
        $campus =  $this->entityManager
            ->getRepository(Campus::class)
            ->findAll();
        return $campus;
    }

}
