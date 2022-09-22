<?php

namespace App\Controller;

use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
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
        UserPasswordHasherInterface $hasher,
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $participant = $participantRepository->find($i);

        if (!$participant) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser()->getUserIdentifier() === $participant->getUserIdentifier()) {
            $form = $this->createForm(ParticipantType::class, $participant);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $nouveau_mdp = $_POST['participant']['newPassword'];
                $confirmation = $_POST['participant']['confirmation'];
                if ($nouveau_mdp) {
                    if (!$confirmation) {
                        $this->addFlash('warning','La confirmation du mot de passe est nécessaire pour sa modification');
                        return $this->redirectToRoute('app_participant_profil',['i' => $participant->getId()]);
                    }
                    if ($nouveau_mdp === $confirmation) {
                        $participant->setPassword($hasher->hashPassword($participant, $nouveau_mdp));
                    } else {
                        $this->addFlash('warning','Le nouveau mot de passe et la confirmation ne correspondent pas');
                        return $this->redirectToRoute('app_participant_profil',['i' => $participant->getId()]);
                    }
                }
                if ($participant->getPictureFileName()) {
                    $participant->setPictureFileName(
                        new File($this->getParameter('image_profil_directory').'/'.$participant->getPictureFileName())
                    );
                }
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    try {
                        $imageFileName = $fileUploader->upload($imageFile);
                        $participant->setPictureFileName($imageFileName);
                    } catch (FileException $e) {
                        $this->addFlash('danger','Une erreur est survenue : ('.$e->getCode().') '.$e->getMessage()); // TODO revoir le message, surtout pour la prod
                        return $this->redirectToRoute('app_participant_profil', ['i' => $participant->getId()]);
                    }
                }

                $entityManager->flush();
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
