<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
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

        $sortie3 = (new Sortie())
            ->setNom('Escape game')
            ->setDateHeureDebut(new \DateTime('now +1 day'))
            ->setDuree(3)
            ->setDateLimiteInscription(new \DateTime('now -8 day'))
            ->setNbInscriptionMax(25)
            ->setInfosSortie('Escape game en équipe, venez découvrir les secret de Grigori Rasputin')
            ->setLieu($this->lieuRepository->find('3'))
            ->setEtat($this->etatRepository->find('2'))
            ->setSiteOrganisateur($this->campusRepository->find('3'))
            ->setOrganisateur($this->participantRepository->find('6'));
        $manager->persist($sortie3);

        // sorties à venir non pleines
        for ($i=1;$i<=30;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(2))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

        // sortie à venir pleines
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(3))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

        // sortie à venir annulée
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(6))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

        // sorties passées - d'un mois
            // sortie pleines
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(5))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

            // sorties non pleines
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(5))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

        // sorties passées + d'un mois
            // sortie pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(31,150);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(5))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

            // sorties non pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(31,150);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(5))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

        // sortie en cours
            // sortie pleines
        for ($i=1;$i<=10;$i++) {
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -5 day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(4))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

            // sorties non pleines
        for ($i=1;$i<=10;$i++) {
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -5 day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(4))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

        // sortie non publiée
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(1))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            $manager->persist($sortie);
        }

        // sortie annulée
            // à venir
                // non pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(6))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

                // pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(6))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

            // passée
                // sortie pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(6))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

                // sorties non pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->participantRepository->find(rand(1,49));
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->lieuRepository->find(rand(1, 15)))
                ->setEtat($this->etatRepository->find(6))
                ->setSiteOrganisateur($this->campusRepository->find(rand(1,4)))
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $id_participant = rand(1,5);
                if ($id_participant !== $organisateur->getId()) {
                    $sortie->addParticipant($this->participantRepository->find($j+$id_participant));
                }
            }
            $manager->persist($sortie);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['sorties'];
    }

    public function getOrder()
    {
        return 6;
    }
}
