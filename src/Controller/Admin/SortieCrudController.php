<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use SebastianBergmann\Type\FalseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SortieCrudController extends AbstractCrudController
{
    private SortieRepository $sortieRepository;

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('nom', 'Nom')->setDisabled(),
            DateField::new('dateHeureDebut', 'Date et heure de début')->setDisabled(),
            IntegerField::new('duree','Durée')->setDisabled(),
            DateField::new('dateLimiteInscription', 'Date limite d\'inscription')->setDisabled(),
            IntegerField::new('nbInscriptionMax', 'Nombre de places')->setDisabled(),
            AssociationField::new('siteOrganisateur', 'Campus')->setDisabled(),
            AssociationField::new('etat','Etat'),
            TextField::new('motifAnnulation', 'Motif d\'annulation'),
        ];
        return $fields;
    }

    public function delete(AdminContext $context)
    {
        $this->addFlash('warning', 'You cannot delete trips.');

        return $this->redirectToRoute('admin');
    }


    public function configureActions(Actions $actions): Actions{
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX,Action::NEW,function(Action $action){
                return $action->setIcon('fas fa-newspaper pe-1')->setLabel('Ajouter un article');
            })
            ->update(Crud::PAGE_INDEX,Action::EDIT,function(Action $action){
                return $action->setIcon('fas fa-edit text-warning')->setLabel('Editer')->addCssClass('text-dark');
            })
            ->remove(Crud::PAGE_INDEX,Action::DELETE,function(Action $action){
                return $action->setIcon('fas fa-trash text-danger')->setLabel('Supprimer')->addCssClass('text-dark');
            })
            ->update(Crud::PAGE_INDEX,Action::DETAIL,function(Action $action){
                return $action->setIcon('fas fa-eye text-info')->setLabel('Consulter')->addCssClass('text-dark');
            });
    }




}
