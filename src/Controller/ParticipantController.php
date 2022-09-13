<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'app_participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/profil/{i}', name: 'profil', methods: ['GET', 'POST'])] // TODO valider que i soit un entier, voir UPDATE pour les méthodes
    public function profil(
        int $i,
        ParticipantRepository $participantRepository,
        Request $request
    ): Response
    {
        $participant = $participantRepository->find($i);

        if (!$participant) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser()->getUserIdentifier() === $participant->getUserIdentifier()) {
            $copie_participant = $participant;
            $form = $this->createForm(ParticipantType::class, $participant);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // TODO gérer les mots de passe
                $participant->setAdministrateur($copie_participant->isAdministrateur())
                    ->setPassword($copie_participant->getPassword())
                    ->setRoles($copie_participant->getRoles())
                    ->setActif($copie_participant->isActif());

                $participantRepository->add($participant, true);
                $this->addFlash('success', 'Votre profil a bien été modifié');
                return $this->redirectToRoute('app_main');
            } else {
                return $this->renderForm('participant/profil.html.twig', [
                    'form' => $form,
                    'participant' => $participant,
                ]);
            }
        } else {
            // TODO : ce n'est pas le profil de la personne connectée, on affiche seulement certaines infos
            $this->addFlash('warning', 'Cette fonctionnalité n\'est pas encore disponible');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('participant/profil.html.twig', []);
    }
}
