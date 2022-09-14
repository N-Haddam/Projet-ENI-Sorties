<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Repository\CampusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function __construct(
        private CampusRepository $campusRepository,
        private UserPasswordHasherInterface $hasher,
    ){}

    public function load(ObjectManager $manager): void
    {
        $admin = (new Participant())
            ->setEmail('admin@sortir.bzh')
            ->setRoles(['ROLE_ADMIN'])
            ->setNom('Admin')
            ->setPrenom('Admin')
            ->setTelephone('0000000000')
            ->setAdministrateur(true)
            ->setCampus($this->campusRepository->find('1'))
            ->setActif(false);
        $admin->setPassword($this->hasher->hashPassword($admin,'admin'));
        $manager->persist($admin);

        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $tab = [0 => false, 1 => true ];
        for ($i=1;$i<=50;$i++) {
            $nom = 'bot' . $i;
            $campus_id = rand(1,5);
            $user = (new Participant())
                ->setEmail($nom . '@campus-eni.fr')
                ->setRoles(['ROLE_USER'])
                ->setNom($nom)
                ->setPrenom($nom)
                ->setTelephone('0000000000')
                ->setAdministrateur(false)
                ->setCampus($this->campusRepository->find($campus_id))
                ->setActif($tab[rand(0,1)]);
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
