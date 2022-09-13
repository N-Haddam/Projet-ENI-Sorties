<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CVilleFixtures extends Fixture implements FixtureGroupInterface
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

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['villes'];
    }
}
