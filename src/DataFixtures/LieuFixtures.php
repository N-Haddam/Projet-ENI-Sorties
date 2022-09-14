<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public function __construct(
        private VilleRepository $villeRepository,
    ){}

    public function load(ObjectManager $manager): void
    {
        $lieu1 = (new Lieu())
            ->setNom('Porz Meilh')
            ->setRue('Kerbaskwin')
            ->setLatitude(48.100316951637524)
            ->setLongitude(-4.423486395138041)
            ->setVille($this->villeRepository->find(1));

        $manager->persist($lieu1);

        $lieu2 = (new Lieu())
            ->setNom('Falaise des Hachettes')
            ->setRue('Rue des poissonniers')
            ->setLatitude(49.3545196966907)
            ->setLongitude(-0.8232153930642908)
            ->setVille($this->villeRepository->find(2));

        $manager->persist($lieu2);

        $lieu3 = (new Lieu())
            ->setNom('Escape Game Paris Prizoners')
            ->setRue('Rue de Quincampoix')
            ->setLatitude(48.85963284325103)
            ->setLongitude(2.3497858565072174)
            ->setVille($this->villeRepository->find(3));

        $manager->persist($lieu3);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['lieus'];
    }

    public function getOrder()
    {
       return 4;
    }
}
