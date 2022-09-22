<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LieuCrudController extends AbstractCrudController
{
    private VilleRepository $villeRepository;
    private LieuRepository $lieuRepository;

    public function __construct(VilleRepository $villeRepository,
                                LieuRepository $lieuRepository,
    )
    {
        $this->villeRepository = $villeRepository;
        $this->lieuRepository = $lieuRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Lieu::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $lieus = $this->lieuRepository->findAll();
        foreach ($lieus as $lieu){
            $codePostal = $lieu->getVille();

            $field = TextField::new($lieu->getVille()->getCodePostal());



        return [
            FormField::addPanel( 'Lieux' )->setIcon( 'fa fa-map-marker'),
            TextField::new('nom', 'Nom'),
            TextField::new('rue', 'Rue'),
            NumberField::new('latitude', 'Latitude'),
            NumberField::new('longitude', 'Longitude'),
            AssociationField::new('ville', 'Ville'),
//            $field
        ];
        }
    }

//    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
//    {
//
//        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
//        dd($formBuilder);
//        $lieu = new Lieu();
//        $form = $this->abstractController->createForm(LieuType::class, $lieu);
//        $request = $this->request;
//        $entityManager = $this->entityManager;
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $villeChoisie = $this->villeRepository->findOneBy(['id'=>$_POST['ville']]);
//
//            $lieu->setVille($villeChoisie);
//            $lieu->setLatitude($_POST['latitude']);
//            $lieu->setLongitude($_POST['longitude']);
//
//
//            $entityManager->persist($lieu);
//            $entityManager->flush();
//            $this->addFlash('success', 'Le nouveau lieu bien été enregistrée');
//        }
//
//        return $formBuilder;
//
//    }

    public function configureActions(Actions $actions): Actions{
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX,Action::NEW,function(Action $action){
                return $action->setIcon('fas fa-newspaper pe-1')->setLabel('Ajouter un article');
            })
            ->update(Crud::PAGE_INDEX,Action::EDIT,function(Action $action){
                return $action->setIcon('fas fa-edit text-warning')->setLabel('Editer')->addCssClass('text-dark');
            })
            ->update(Crud::PAGE_INDEX,Action::DELETE,function(Action $action){
                return $action->setIcon('fas fa-trash text-danger')->setLabel('Supprimer')->addCssClass('text-dark');
            })
            ->update(Crud::PAGE_INDEX,Action::DETAIL,function(Action $action){
                return $action->setIcon('fas fa-eye text-info')->setLabel('Consulter')->addCssClass('text-dark');
            });
    }

}
