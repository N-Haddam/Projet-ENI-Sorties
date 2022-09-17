<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant', name: 'app_participant_')]
class ParticipantController extends AbstractController
{
    #[Route('/profil/{i}', name: 'profil', requirements: ['i' => '\d+'], methods: ['GET', 'POST'])]
    public function profil(
        int $i,
        ParticipantRepository $participantRepository,
        Request $request,
        UserPasswordHasherInterface $hasher
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
                $nouvea_mdp = $_POST['participant']['newPassword'];
                $confirmation = $_POST['participant']['confirmation'];
                if ($nouvea_mdp) {
                    if (!$confirmation) {
                        $this->addFlash('warning','La confirmation du mot de passe est nécessaire pour sa modification');
                        return $this->redirectToRoute('app_participant_profil',['i' => $participant->getId()]);
                    }
                    if ($nouvea_mdp === $confirmation) {
                        $participant->setPassword($hasher->hashPassword($participant, $nouvea_mdp));
                    } else {
                        $this->addFlash('warning','Le nouveau mot de passe et la confirmation ne correspondent pas');
                        return $this->redirectToRoute('app_participant_profil',['i' => $participant->getId()]);
                    }
                }
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
            return $this->render('participant/profil.html.twig', [
                'participant' => $participant,
            ]);
        }
    }
}
