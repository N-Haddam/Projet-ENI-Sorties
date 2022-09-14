<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
//    public function __construct(
//        private LieuRepository $lieuRepository,
//    ){}

    public function load(ObjectManager $manager): void
    {
        $poullanSurMer = (new Ville())
            ->setNom('Poullan-sur-Mer')
            ->setCodePostal(29100);


        $manager->persist($poullanSurMer);

         $bayeux = (new Ville())
        ->setNom('Bayeux')
        ->setCodePostal(14400);

        $manager->persist($bayeux);

        $Paris75001 = (new Ville())
            ->setNom('Paris')
            ->setCodePostal(75001);

        $manager->persist($Paris75001);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['villes'];
    }

    public function getOrder()
    {
        return 3;
    }
}
