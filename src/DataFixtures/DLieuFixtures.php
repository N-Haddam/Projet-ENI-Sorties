<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DLieuFixtures extends Fixture implements FixtureGroupInterface
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

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['lieus'];
    }
}
