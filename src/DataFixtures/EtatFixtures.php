<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture implements OrderedFixtureInterface
{
    public const CREEE_ETAT_REFERENCE = 'creee-etat';
    public const OUVERTE_ETAT_REFERENCE = 'ouverte-etat';
    public const CLOTUREE_ETAT_REFERENCE = 'cloturee-etat';
    public const EN_COURS_ETAT_REFERENCE = 'en-cours-etat';
    public const PASSEE_ETAT_REFERENCE = 'passee-etat';
    public const ANNULEE_ETAT_REFERENCE = 'annulee-etat';

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

        $this->addReference(self::CREEE_ETAT_REFERENCE, $etat1);
        $this->addReference(self::OUVERTE_ETAT_REFERENCE, $etat2);
        $this->addReference(self::CLOTUREE_ETAT_REFERENCE, $etat3);
        $this->addReference(self::EN_COURS_ETAT_REFERENCE, $etat4);
        $this->addReference(self::PASSEE_ETAT_REFERENCE, $etat5);
        $this->addReference(self::ANNULEE_ETAT_REFERENCE, $etat6);
    }

    public function getOrder()
    {
       return 5;
    }
}
