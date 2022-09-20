<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    public const NANTES_CAMPUS_REFERENCE = 'nantes-campus';
    public const RENNES_CAMPUS_REFERENCE = 'rennes-campus';
    public const QUIMPER_CAMPUS_REFERENCE = 'quimper-campus';
    public const NIORT_CAMPUS_REFERENCE = 'niort-campus';

    public function load(ObjectManager $manager): void
    {
        $nantes = (new Campus())->setNom('Nantes');
        $manager->persist($nantes);

        $rennes = (new Campus())->setNom('Rennes');
        $manager->persist($rennes);

        $quimper = (new Campus())->setNom('Quimper');
        $manager->persist($quimper);

        $niort = (new Campus())->setNom('Niort');
        $manager->persist($niort);

        $manager->flush();

        $this->addReference(self::NANTES_CAMPUS_REFERENCE, $nantes);
        $this->addReference(self::RENNES_CAMPUS_REFERENCE, $rennes);
        $this->addReference(self::QUIMPER_CAMPUS_REFERENCE, $quimper);
        $this->addReference(self::NIORT_CAMPUS_REFERENCE, $niort);
    }

    public static function getGroups(): array
    {
        return ['campus'];
    }

    public function getOrder()
    {
        return 1;
    }
}
