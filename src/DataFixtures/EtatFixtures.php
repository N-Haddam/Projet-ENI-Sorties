<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture implements OrderedFixtureInterface
{
    public const LISTE_ETATS_REFERENCE = 'liste-etats';

    public function load(ObjectManager $manager): void
    {

        $etat1 = (new Etat())->setLibelle('Créée');
        $manager->persist($etat1);

        $etat2 = (new Etat())->setLibelle('Ouverte');
        $manager->persist($etat2);

        $etat3 = (new Etat())->setLibelle('Clôturée');
        $manager->persist($etat3);

        $etat4 = (new Etat())->setLibelle('Activité en cours');
        $manager->persist($etat4);

        $etat5 = (new Etat())->setLibelle('Passée');
        $manager->persist($etat5);

        $etat6 = (new Etat())->setLibelle('Annulée');
        $manager->persist($etat6);

        $manager->flush();

        $this->addReference(self::LISTE_ETATS_REFERENCE, $etat1, $etat2);
    }

    public function getOrder()
    {
       return 5;
    }
}
