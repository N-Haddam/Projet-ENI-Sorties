<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

         $etat1 = (new Etat())
            ->setLibelle('Ouverte');
         $manager->persist($etat1);

        $etat2 = (new Etat())
            ->setLibelle('Fermee');
        $manager->persist($etat2);

        //TODO ajouter les autres

        $manager->flush();
    }

    public function getOrder()
    {
       return 5;
    }
}
