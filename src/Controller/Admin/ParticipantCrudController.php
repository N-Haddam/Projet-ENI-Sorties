<?php

namespace App\Controller\Admin;

use App\Repository\ParticipantRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

class ParticipantCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordEncoder;
    private ParticipantRepository $participantRepository;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, ParticipantRepository $participantRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->participantRepository = $participantRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            FormField::addPanel( 'Participants' )->setIcon( 'fa fa-user' ),
            TextField::new('email', 'Mail'),
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            TextField::new('pseudo', 'Pseudo'),
            TextField::new('telephone', 'Téléphone'),
            AssociationField::new('campus', 'Campus'),
            CollectionField::new('Roles','Roles'),
            BooleanField::new('administrateur', 'Administrateur'),
            BooleanField::new('actif', 'Actif'),
            TextField::new('password','Password')
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms(),
        ];
        return $fields;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        $this->addEncodePasswordEventListener($formBuilder);

        return $formBuilder;
    }

    protected function addEncodePasswordEventListener(FormBuilderInterface $formBuilder)
    {
        $participantRepository = $this->participantRepository;
        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($participantRepository) {

            $user = $event->getData();

            if ($user->getPassword()) {

                $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));

                $participantRepository->add($user, true);
            }
        });
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
            ->update(Crud::PAGE_INDEX,Action::DELETE,function(Action $action){
                return $action->setIcon('fas fa-trash text-danger')->setLabel('Supprimer')->addCssClass('text-dark');
            })
            ->update(Crud::PAGE_INDEX,Action::DETAIL,function(Action $action){
                return $action->setIcon('fas fa-eye text-info')->setLabel('Consulter')->addCssClass('text-dark');
            });
    }

}
