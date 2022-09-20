<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LieuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lieu::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel( 'Lieux' )->setIcon( 'fa fa-map-marker'),
            TextField::new('nom', 'Nom'),
            TextField::new('rue', 'Rue'),
            IntegerField::new('latitude', 'Latitude'),
            IntegerField::new('longitude', 'Longitude'),
            AssociationField::new('ville', 'Ville'),
        ];
    }

}
