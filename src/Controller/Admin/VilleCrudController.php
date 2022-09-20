<?php

namespace App\Controller\Admin;

use App\Entity\Ville;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VilleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ville::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel( 'Ville' )->setIcon( 'fa fa-globe'),
            TextField::new('nom', 'Nom'),
            IntegerField::new('codePostal', 'Code Postal'),
        ];
    }

}
