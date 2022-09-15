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

        $lieu4 = (new Lieu())
            ->setNom('Sur Mesure Bouffay')
            ->setRue('15 Rue Beauregard')
            ->setLatitude(47.215389827418605)
            ->setLongitude(-1.5553520306822886)
            ->setVille($this->villeRepository->find(4));
        $manager->persist($lieu4);

        $lieu5 = (new Lieu())
            ->setNom('Parc de procé')
            ->setRue('44 Rue des Dervallières')
            ->setLatitude(47.2234867)
            ->setLongitude(-1.5843714)
            ->setVille($this->villeRepository->find(4));
        $manager->persist($lieu5);

        $lieu6 = (new Lieu())
            ->setNom('Stade de la Beaujoire')
            ->setRue('Route de Saint-Joseph')
            ->setLatitude(47.2560233)
            ->setLongitude(-1.524675)
            ->setVille($this->villeRepository->find(7));
        $manager->persist($lieu6);

        $lieu7 = (new Lieu())
            ->setNom('Le Ferrailleur')
            ->setRue('21 Quai des Antilles')
            ->setLatitude(47.2005543)
            ->setLongitude(-1.5759229)
            ->setVille($this->villeRepository->find(6));
        $manager->persist($lieu7);

        $lieu8 = (new Lieu())
            ->setNom('Ferme de Quincé')
            ->setRue('lieu dit, Le Haut Quincé')
            ->setLatitude(48.1368351)
            ->setLongitude(-1.7027112)
            ->setVille($this->villeRepository->find(11));
        $manager->persist($lieu8);

        $lieu9 = (new Lieu())
            ->setNom('Le Bacchus')
            ->setRue('3 Esplanade Julie Rose Calvé')
            ->setLatitude(48.1085455)
            ->setLongitude(-1.6995807)
            ->setVille($this->villeRepository->find(9));
        $manager->persist($lieu9);

        $lieu10 = (new Lieu())
            ->setNom('Rue de la soif')
            ->setRue('Rue Saint-Michel')
            ->setLatitude(48.1141989)
            ->setLongitude(-1.6835425)
            ->setVille($this->villeRepository->find(9));
        $manager->persist($lieu10);

        $lieu11 = (new Lieu())
            ->setNom('Saint Andrew\'s')
            ->setRue('11 Pl. du Stivel')
            ->setLatitude(47.990163)
            ->setLongitude(-4.1130304)
            ->setVille($this->villeRepository->find(12));
        $manager->persist($lieu11);

        $lieu12 = (new Lieu())
            ->setNom('Le Novomax')
            ->setRue('2 Bd Dupleix')
            ->setLatitude(47.9950312)
            ->setLongitude(-4.0990832)
            ->setVille($this->villeRepository->find(12));
        $manager->persist($lieu12);

        $lieu13 = (new Lieu())
            ->setNom('Parc Camille-Richard')
            ->setRue('Bd Jean Cocteau')
            ->setLatitude(46.324397)
            ->setLongitude(-0.4330732)
            ->setVille($this->villeRepository->find(13));
        $manager->persist($lieu13);

        $lieu14 = (new Lieu())
            ->setNom('Jardin des plantes')
            ->setRue('Allée haute du Jardin des Plantes')
            ->setLatitude(46.3311947)
            ->setLongitude(-0.462019)
            ->setVille($this->villeRepository->find(13));
        $manager->persist($lieu14);

        $lieu15 = (new Lieu())
            ->setNom('La Cervoiserie Niort')
            ->setRue('4 Rue Sainte-Claire Déville')
            ->setLatitude(46.3158653)
            ->setLongitude(-0.495892)
            ->setVille($this->villeRepository->find(13));
        $manager->persist($lieu15);

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
