<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ){}

    public function load(ObjectManager $manager): void
    {
        $nantesCampus = $this->getReference(CampusFixtures::NANTES_CAMPUS_REFERENCE);
        $rennesCampus = $this->getReference(CampusFixtures::RENNES_CAMPUS_REFERENCE);
        $quimperCampus = $this->getReference(CampusFixtures::QUIMPER_CAMPUS_REFERENCE);
        $niortCampus = $this->getReference(CampusFixtures::NIORT_CAMPUS_REFERENCE);
        $listeCampus = [
            0 => $nantesCampus,
            1 => $rennesCampus,
            2 => $quimperCampus,
            3 => $niortCampus,
        ];

        $admin = (new Participant())
            ->setEmail('admin@sortir.bzh')
            ->setRoles(['ROLE_ADMIN'])
            ->setNom('Admin')
            ->setPrenom('Admin')
            ->setTelephone('0000000000')
            ->setAdministrateur(true)
            ->setCampus($listeCampus[rand(0,3)])
            ->setActif(false)
            ->setPseudo('admin');
        $admin->setPassword($this->hasher->hashPassword($admin,'admin'));
        $manager->persist($admin);

        $tab = [0 => false, 1 => true ];
        for ($i=1;$i<=50;$i++) {
            $nom = 'bot' . $i;
            $user = (new Participant())
                ->setEmail($nom . '@campus-eni.fr')
                ->setRoles(['ROLE_USER'])
                ->setNom($nom)
                ->setPrenom($nom)
                ->setTelephone('0000000000')
                ->setAdministrateur(false)
                ->setCampus($listeCampus[rand(0,3)])
                ->setActif($tab[rand(0,1)])
                ->setPseudo($nom);
            $user->setPassword($this->hasher->hashPassword($user, 'test'));
            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['users'];
    }

    public function getOrder()
    {
        return 2;
    }
}
