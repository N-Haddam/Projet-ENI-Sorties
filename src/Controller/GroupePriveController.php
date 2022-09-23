<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Entity\Participant;
use App\Form\GroupeType;
use App\Repository\GroupePriveRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupePriveController extends AbstractController
{
    #[Route('/groupe/creer', name: 'app_groupe_creer', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        GroupePriveRepository $groupePriveRepository,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        PaginatorInterface $paginator,
    ): Response
    {
//        if ($request->getMethod() === 'GET') {
//            $params = ['portion' => ''];
//        } else {
//            $params = $_POST;
//        }
//        $query = $participantRepository->findByRequest($params);
//        $pagination = $paginator->paginate($query, $request->query->getInt('page',1),10);
        $participants = $participantRepository->findAll();
        $groupe = new GroupePrive();
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);




        if ($form->isSubmitted() && $form->isValid()) {
            $organisateur = $request->getUser();
            $groupe->setOrganisateur($organisateur);
            $participants[]= new Participant();
            $participants = $_POST['participant'];


            $entityManager->persist($groupe);
            $entityManager->flush();
            $this->addFlash('success', 'Le nouveau groupe à bien été enregistré');
            return $this->redirectToRoute('app_main');
        }

        return $this->renderForm('groupe_prive/creer.html.twig', [
//            "pagination" => $pagination,
            'form' => $form,
            'participants' => $participants,
            'groupe' => $groupe,
//            "params" => $params,
        ]);
    }

    #[Route('/groupe/list', name: 'app_groupe_list')]
    public function list(
        Request $request,
        GroupePriveRepository $groupePriveRepository,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
    ){
        $user = $this->getUser();

        $groupes = $groupePriveRepository->findBy(['organisateur' => $user->getId()]);

        return $this->render('groupe_prive/list.html.twig', [
            'groupes' => $groupes,
        ]);
    }

//    #[Route('/groupe/creer', name: 'app_groupe_creer', methods: ['GET'])]
//    public function ajouterParticipant(){
//        $participantsAAjouter[] = new Participant();
//        $participantsAAjouter = $_POST;
////        dd($participantsAAjouter);
//    }
}
