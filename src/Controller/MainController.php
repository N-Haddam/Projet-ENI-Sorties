<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    public EntityManagerInterface $entityManager;
    private Response $response;

    #[Route('/', name: 'app_default', methods: ['GET'])]
    public function default(): Response {
        return $this->redirectToRoute('app_main');
    }

    #[Route('/main', name: 'app_main', methods: ['GET', 'POST'])]
    public function list(
        Request $request,
        SortieRepository $sortieRepository,
        CampusRepository $campusRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $user = $this->getUser();
        $userCampus = $user->getCampus();

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
        $params['methode'] = strtolower($request->getMethod());


        $query = $sortieRepository->findByRequest($params);
        $pagination = $paginator->paginate($query, $request->query->getInt('page',1),10);



        return $this->render('main/index.html.twig', [
            "pagination" => $pagination,
            "listeCampus" => $campusRepository->findAll(),
            "params" => $params,
        ]);
    }
}
