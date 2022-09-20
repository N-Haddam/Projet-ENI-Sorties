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
        $this->addReference('ville1-ville', $poullanSurMer);

        $bayeux = (new Ville())
            ->setNom('Bayeux')
            ->setCodePostal(14400);
        $manager->persist($bayeux);
        $this->addReference('ville2-ville', $bayeux);

        $Paris75001 = (new Ville())
            ->setNom('Paris')
            ->setCodePostal(75001);
        $manager->persist($Paris75001);
        $this->addReference('ville3-ville', $Paris75001);

        $nantes44000 = (new Ville())
            ->setNom('Nantes')
            ->setCodePostal(44000);
        $manager->persist($nantes44000);
        $this->addReference('ville4-ville', $nantes44000);

        $nantes44100 = (new Ville())
            ->setNom('Nantes')
            ->setCodePostal(44100);
        $manager->persist($nantes44100);
        $this->addReference('ville5-ville', $nantes44100);

        $nantes44200 = (new Ville())
            ->setNom('Nantes')
            ->setCodePostal(44200);
        $manager->persist($nantes44200);
        $this->addReference('ville6-ville', $nantes44200);

        $nantes44300 = (new Ville())
            ->setNom('Nantes')
            ->setCodePostal(44300);
        $manager->persist($nantes44300);
        $this->addReference('ville7-ville', $nantes44300);

        $nantes44400 = (new Ville())
            ->setNom('Nantes')
            ->setCodePostal(44400);
        $manager->persist($nantes44400);
        $this->addReference('ville8-ville', $nantes44400);

        $rennes35000 = (new Ville())
            ->setNom('Rennes')
            ->setCodePostal(35000);
        $manager->persist($rennes35000);
        $this->addReference('ville9-ville', $rennes35000);

        $rennes35200 = (new Ville())
            ->setNom('Rennes')
            ->setCodePostal(35200);
        $manager->persist($rennes35200);
        $this->addReference('ville10-ville', $rennes35200);

        $rennes35700 = (new Ville())
            ->setNom('Rennes')
            ->setCodePostal(35700);
        $manager->persist($rennes35700);
        $this->addReference('ville11-ville', $rennes35700);

        $quimper = (new Ville())
            ->setNom('Quimper')
            ->setCodePostal(29000);
        $manager->persist($quimper);
        $this->addReference('ville12-ville', $quimper);

        $niort = (new Ville())
            ->setNom('Niort')
            ->setCodePostal(79000);
        $manager->persist($niort);
        $this->addReference('ville13-ville', $niort);

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
