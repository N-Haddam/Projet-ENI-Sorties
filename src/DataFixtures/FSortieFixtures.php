<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use APP\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class FSortieFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private LieuRepository $lieuRepository,
        private EtatRepository $etatRepository,
        private CampusRepository $campusRepository,
        private ParticipantRepository $participantRepository,
    ){}

    public function load(ObjectManager $manager): void
    {
        $sortie1 = (new Sortie())
            ->setNom('Pêche à pied')
            ->setDateHeureDebut(new \DateTime('now +14 day'))
            ->setDuree(3)
            ->setDateLimiteInscription(new \DateTime('now +7 day'))
            ->setNbInscriptionMax(15)
            ->setInfosSortie('Apporter des habits chauds et imperméable, merci de respecter la maille')
            ->setLieu($this->lieuRepository->find('1'))
            ->setEtat($this->etatRepository->find('1'))
            ->setSiteOrganisateur($this->campusRepository->find('1'))
            ->setOrganisateur($this->participantRepository->find('1'));

        $manager->persist($sortie1);

        $sortie2 = (new Sortie())
            ->setNom('Visite du stratotype du bajocien')
            ->setDateHeureDebut(new \DateTime('now +29 day'))
            ->setDuree(3)
            ->setDateLimiteInscription(new \DateTime('now +19 day'))
            ->setNbInscriptionMax(25)
            ->setInfosSortie('Prévoir un marteau de géologue, une boussole, une gourde, et de quoi prendre des notes')
            ->setLieu($this->lieuRepository->find('2'))
            ->setEtat($this->etatRepository->find('1'))
            ->setSiteOrganisateur($this->campusRepository->find('2'))
            ->setOrganisateur($this->participantRepository->find('3'));

        $manager->persist($sortie2);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['sorties'];
    }

}
