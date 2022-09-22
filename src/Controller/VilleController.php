<?php

namespace App\Controller;

use App\Entity\Ville;
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
    #[IsGranted('ROLE_ADMIN')]
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
    public function ajouter(
        VilleRepository $villeRepository
    ): Response
    {
        if (!$this->getUser()->isActif()) {
            throw $this->createAccessDeniedException();
        }

        if (isset($_GET['nom']) && $_GET['nom'] !== '' && isset($_GET['cp']) && strlen($_GET['cp']) === 5) {
            $test = $villeRepository->findOneBy(['nom' => $_GET['nom'], 'codePostal' => $_GET['cp']]);
            if ($test) {
                $this->addFlash('danger', 'La ville est déjà enregistrée');
                return $this->redirectToRoute('app_ville_liste');
            }

            $nouvelleVille = (new Ville())
                ->setNom(ucfirst($_GET['nom']))
                ->setCodePostal($_GET['cp']);
            $villeRepository->add($nouvelleVille, true);
            $this->addFlash('success',
                'La ville a bien été ajouté : '.$nouvelleVille->getNom().' ('.$nouvelleVille->getCodePostal().')');
        } else {
            // TODO détailler les cas pour plus de précision dans les flash
            $this->addFlash('danger', 'La ville n\'a pas pu être ajoutée...');
        }
        return $this->redirectToRoute('app_ville_liste');
    }
}
