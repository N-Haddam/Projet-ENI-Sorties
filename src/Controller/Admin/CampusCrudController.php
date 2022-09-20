<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CampusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Campus::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel( 'Campus' )->setIcon( 'fa fa-graduation-cap'),
            TextField::new('nom', 'Nom'),
        ];
    }

    Public function configureActions(Actions $actions): Actions{
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
