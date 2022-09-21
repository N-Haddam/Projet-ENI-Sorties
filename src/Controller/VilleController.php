<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'app_ville_')]
class VilleController extends AbstractController
{
    #[Route('/liste', name: 'liste', methods: ['GET', 'POST'])]
    public function liste(
        Request $request,
        VilleRepository $villeRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        if (!$this->getUser()->isActif()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->getMethod() === 'GET') {
            $params = ['portion' => ''];
        } else {
            $params = $_POST;
        }

        $query = $villeRepository->findByRequest($params);
        $pagination = $paginator->paginate($query, $request->query->getInt('page',1),10);

        return $this->render('ville/liste.html.twig', [
            "pagination" => $pagination,
            "params" => $params,
        ]);
    }

    #[Route('/ajouter', name: 'ajouter', methods: ['GET'])]
    public function ajouter(): Response
    {
        if (!$this->getUser()->isActif()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('liste.html.twig', [ // TODO modifier le template
            'controller_name' => 'VilleController',
        ]);
    }
}
