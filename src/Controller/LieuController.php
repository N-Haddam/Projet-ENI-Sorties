<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/lieu', name: 'app_sortie_')]
class LieuController extends AbstractController
{
    #[Route('/gerer', name: 'gerer')]
    public function create(
        Request $request,
        LieuRepository $lieuRepository,
        EntityManagerInterface $entityManager,
        VilleRepository $villeRepository,
    ): Response
    {
        $villes = $villeRepository->findAll();
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', 'Le nouveau lieu bien été enregistrée');
            return $this->redirectToRoute('app_main');
        }

        return $this->renderForm('lieu/gerer.html.twig', [
            'form' => $form,
            'villes' => $villes,
            'lieu' => $lieu
        ]);
    }
}
