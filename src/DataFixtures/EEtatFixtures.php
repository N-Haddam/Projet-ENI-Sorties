<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EEtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

         $etat = (new Etat())
            ->setLibelle('Ouverte');
         $manager->persist($etat);

        $manager->flush();
    }
}
