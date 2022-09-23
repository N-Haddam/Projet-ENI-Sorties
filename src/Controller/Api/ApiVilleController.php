<?php

namespace App\Controller\Api;

use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ville', name: 'api_ville_')]
class ApiVilleController extends AbstractController
{
    #[Route('/ajouter', name: 'ajouter', methods: ['GET'])]
    public function ajouter(
        Request $request,
        VilleRepository $villeRepository,
    ): Response
    {
        $nom = $_GET['nom'];
        $cp = $_GET['cp'];

        if ($nom !== '' && strlen($cp) === 5) {
            $test = $villeRepository->findOneBy(['nom' => $nom, 'codePostal' => $cp]);
            if ($test) {
                $reponse = ['422', "La ville existe déjà"];
            }

            $nouvelleVille = (new Ville())
                ->setNom(ucfirst($nom))
                ->setCodePostal($cp);
            $villeRepository->add($nouvelleVille, true);
            $reponse = ['200', "La ville a bien été ajoutée : $nom ($cp)", $nouvelleVille->getId()];

        } else {
            $reponse = ['418', 'La ville n\'a pas pu être ajoutée...'];
        }

        return $this->json(
            $reponse,
            $reponse[0],
            [],
            []
        );
    }
}
