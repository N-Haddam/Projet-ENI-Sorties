<?php

namespace App\DataFixtures;

use App\Entity\Etat;
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
    private array $listeCampus;
    private int $sizeListeCampus;

    private Etat $etatEnCours;
    private Etat $etatOuvert;
    private Etat $etatClot;
    private Etat $etatAnnu;
    private Etat $etatCree;

    private array $listeEtatsAvenir;
    private array $listeEtatsPasses;

    private int $sizeListeEtatsAvenir;
    private int $sizeListeEtatsPasses;

//    private array $listeEtats;
//    private int $sizeListeEtats;

    private array $listeLieux;
    private int $sizeListeLieux;
    private array $listeVilles;
    private int $sizeListeVilles;
    private array $listeParticipants;
    private int $sizeListeParticipants;

//    public function __construct(
//        private LieuRepository $lieuRepository,
//        private EtatRepository $etatRepository,
//        private CampusRepository $campusRepository,
//        private ParticipantRepository $participantRepository,
//    ){}

    public function load(ObjectManager $manager): void
    {
        $this->loadOtherFixtures();

        $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
        $sortie1 = (new Sortie())
            ->setNom('Pêche à pied')
            ->setDateHeureDebut(new \DateTime('now +14 day'))
            ->setDuree(3)
            ->setDateLimiteInscription(new \DateTime('now +7 day'))
            ->setNbInscriptionMax(15)
            ->setInfosSortie('Apporter des habits chauds et imperméable, merci de respecter la maille')
            ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
            ->setEtat($this->listeEtatsAvenir[rand(0,$this->sizeListeEtatsAvenir-1)])
            ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
            ->setOrganisateur($organisateur)
            ->addParticipant($organisateur);
        $manager->persist($sortie1);

        $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
        $sortie2 = (new Sortie())
            ->setNom('Visite du stratotype du bajocien')
            ->setDateHeureDebut(new \DateTime('now +29 day'))
            ->setDuree(3)
            ->setDateLimiteInscription(new \DateTime('now +19 day'))
            ->setNbInscriptionMax(25)
            ->setInfosSortie('Prévoir un marteau de géologue, une boussole, une gourde, et de quoi prendre des notes')
            ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
            ->setEtat($this->listeEtatsAvenir[rand(0,$this->sizeListeEtatsAvenir-1)])
            ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
            ->setOrganisateur($this->listeParticipants[rand(0,$this->sizeListeParticipants-1)])
            ->addParticipant($organisateur);
        $manager->persist($sortie2);

        $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
        $sortie3 = (new Sortie())
            ->setNom('Escape game')
            ->setDateHeureDebut(new \DateTime('now +1 day'))
            ->setDuree(3)
            ->setDateLimiteInscription(new \DateTime('now -8 day'))
            ->setNbInscriptionMax(25)
            ->setInfosSortie('Escape game en équipe, venez découvrir les secret de Grigori Rasputin')
            ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
            ->setEtat($this->listeEtatsAvenir[rand(0,$this->sizeListeEtatsAvenir-1)])
            ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
            ->setOrganisateur($this->listeParticipants[rand(0,$this->sizeListeParticipants-1)])
            ->addParticipant($organisateur);
        $manager->persist($sortie3);

        // sorties à venir non pleines
        for ($i=1;$i<=30;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->listeEtatsAvenir[rand(0,$this->sizeListeEtatsAvenir-1)])
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

        // sortie à venir pleines
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+30)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatClot)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

        // sortie à venir annulée
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+50)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatAnnu)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

        // sorties passées - d'un mois
            // sortie pleines
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+70)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatClot)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

            // sorties non pleines
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+90)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->listeEtatsPasses[rand(0,$this->sizeListeEtatsPasses-1)])
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

        // sorties passées + d'un mois
            // sortie pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(31,150);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+120)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->listeEtatsPasses[rand(0,$this->sizeListeEtatsPasses-1)])
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

            // sorties non pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(31,150);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+130)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->listeEtatsPasses[rand(0,$this->sizeListeEtatsPasses-1)])
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

        // sortie en cours
            // sortie pleines
        for ($i=1;$i<=10;$i++) {
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+140)
                ->setDateHeureDebut(new \DateTime('now'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -5 day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatClot)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

            // sorties non pleines
        for ($i=1;$i<=10;$i++) {
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+150)
                ->setDateHeureDebut(new \DateTime('now'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -5 day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatOuvert)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

        // sortie non publiée
        for ($i=1;$i<=20;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+160)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatCree)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
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
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+180)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatAnnu)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

                // pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(1,40);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+190)
                ->setDateHeureDebut(new \DateTime('now +' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now +' . $jour_debut-5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatAnnu)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

            // passée
                // sortie pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+200)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatAnnu)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit; $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
                }
            }
            $manager->persist($sortie);
        }

                // sorties non pleines
        for ($i=1;$i<=10;$i++) {
            $jour_debut = rand(1,28);
            $nb_inscrit = rand(10,25);
            $organisateur = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
            $sortie = (new Sortie())
                ->setNom('Sortie ' . $i+210)
                ->setDateHeureDebut(new \DateTime('now -' . $jour_debut . ' day'))
                ->setDuree(rand(30,180))
                ->setDateLimiteInscription(new \DateTime('now -' . $jour_debut+5 . ' day'))
                ->setNbInscriptionMax($nb_inscrit)
                ->setInfosSortie('Bla bla bla lorem ipsum etc.')
                ->setLieu($this->listeLieux[rand(0,$this->sizeListeLieux-1)])
                ->setEtat($this->etatAnnu)
                ->setSiteOrganisateur($this->listeCampus[rand(0,$this->sizeListeCampus-1)])
                ->setOrganisateur($organisateur)
                ->addParticipant($organisateur);
            for ($j = 1; $j <= $nb_inscrit-(rand(1,9)); $j++) {
                $participant = $this->listeParticipants[rand(0,$this->sizeListeParticipants-1)];
                if ($participant->getId() !== $organisateur->getId()) {
                    $sortie->addParticipant($participant);
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

    private function loadOtherFixtures() {
        $this->listeCampus = [
            $this->getReference(CampusFixtures::NANTES_CAMPUS_REFERENCE),
            $this->getReference(CampusFixtures::RENNES_CAMPUS_REFERENCE),
            $this->getReference(CampusFixtures::QUIMPER_CAMPUS_REFERENCE),
            $this->getReference(CampusFixtures::NIORT_CAMPUS_REFERENCE),
        ];
        $this->sizeListeCampus = count($this->listeCampus);

        $this->etatEnCours = $this->getReference(EtatFixtures::EN_COURS_ETAT_REFERENCE);
        $this->etatOuvert = $this->getReference(EtatFixtures::OUVERTE_ETAT_REFERENCE);
        $this->etatClot = $this->getReference(EtatFixtures::CLOTUREE_ETAT_REFERENCE);
        $this->etatAnnu = $this->getReference(EtatFixtures::ANNULEE_ETAT_REFERENCE);
        $this->etatCree = $this->getReference(EtatFixtures::CREEE_ETAT_REFERENCE);

        $this->listeEtatsAvenir = [
            $this->getReference(EtatFixtures::CREEE_ETAT_REFERENCE),
            $this->getReference(EtatFixtures::OUVERTE_ETAT_REFERENCE),
            $this->getReference(EtatFixtures::CLOTUREE_ETAT_REFERENCE),
        ];
        $this->sizeListeEtatsAvenir = count($this->listeEtatsAvenir);

        $this->listeEtatsPasses = [
            $this->getReference(EtatFixtures::PASSEE_ETAT_REFERENCE),
            $this->getReference(EtatFixtures::ANNULEE_ETAT_REFERENCE),
        ];
        $this->sizeListeEtatsPasses = count($this->listeEtatsPasses);


//        $this->listeEtats = [
//
//            $this->getReference(EtatFixtures::EN_COURS_ETAT_REFERENCE),
//
//        ];
//        $this->sizeListeEtats = count($this->listeEtats);



        $this->listeLieux = [
            $this->getReference('lieu1-lieu'),
            $this->getReference('lieu2-lieu'),
            $this->getReference('lieu3-lieu'),
            $this->getReference('lieu4-lieu'),
            $this->getReference('lieu5-lieu'),
            $this->getReference('lieu6-lieu'),
            $this->getReference('lieu7-lieu'),
            $this->getReference('lieu8-lieu'),
            $this->getReference('lieu9-lieu'),
            $this->getReference('lieu10-lieu'),
            $this->getReference('lieu11-lieu'),
            $this->getReference('lieu12-lieu'),
            $this->getReference('lieu13-lieu'),
            $this->getReference('lieu14-lieu'),
            $this->getReference('lieu15-lieu'),
        ];
        $this->sizeListeLieux = count($this->listeLieux);
        $this->listeVilles = [
            $this->getReference('ville1-ville'),
            $this->getReference('ville2-ville'),
            $this->getReference('ville3-ville'),
            $this->getReference('ville4-ville'),
            $this->getReference('ville5-ville'),
            $this->getReference('ville6-ville'),
            $this->getReference('ville7-ville'),
            $this->getReference('ville8-ville'),
            $this->getReference('ville9-ville'),
            $this->getReference('ville10-ville'),
            $this->getReference('ville11-ville'),
            $this->getReference('ville12-ville'),
            $this->getReference('ville13-ville'),
        ];
        $this->sizeListeVilles = count($this->listeVilles);
        for($i=1;$i<=51;$i++) {
            $this->listeParticipants[] = $this->getReference('user'.$i.'-participant');
        }
        $this->sizeListeParticipants = count($this->listeParticipants);
    }

}
