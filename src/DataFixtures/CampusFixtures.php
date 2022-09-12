<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $campus_admin = (new Campus())->setNom('Admin campus');
        $manager->persist($campus_admin);

        $nantes = (new Campus())->setNom('Nantes');
        $manager->persist($nantes);

        $rennes = (new Campus())->setNom('Rennes');
        $manager->persist($rennes);

        $quimper = (new Campus())->setNom('Quimper');
        $manager->persist($quimper);

        $niort = (new Campus())->setNom('Niort');
        $manager->persist($niort);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['campus'];
    }
}
