<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(ParticipantCrudController::class)->generateUrl();
        return $this->redirect($url);
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
//         return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projet Sorties');
    }

    public function configureMenuItems(): iterable
    {
        return [
//            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            yield MenuItem::linkToUrl('Accueil AppSortir', 'fa fa-home', $this->generateUrl('app_main')),
            yield MenuItem::section('Utilisateurs'),
            yield MenuItem::linkToCrud('Participants', 'fa fa-user', Participant::class),
            yield MenuItem::section('Sorties'),
            yield MenuItem::linkToCrud('Sorties', 'fa fa-suitcase', Sortie::class),
            yield MenuItem::section('Campus'),
            yield MenuItem::linkToCrud('Campus', 'fa fa-graduation-cap', Campus::class),
            yield MenuItem::section('Ville'),
            yield MenuItem::linkToCrud('Villes', 'fa fa-globe', Ville::class),
            yield MenuItem::section('Lieu'),
            yield MenuItem::linkToCrud('Lieux', 'fa fa-map-marker', Lieu::class),
        ];
    }
}
