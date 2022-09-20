<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\EventListener\Archivage;
use ContainerC8JBeMB\get_ServiceLocator_DuP8CuService;
use ContainerC8JBeMB\getSortieRepositoryService;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;


/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    private const DAYS_BEFORE_REMOVAL = 30;
    private $etatRepository;
    private $campusRepository;

    public function __construct(ManagerRegistry $registry, EtatRepository $etatRepository, CampusRepository $campusRepository)
    {
        $this->etatRepository = $etatRepository;
        $this->campusRepository = $campusRepository;
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @throws Exception
     */
    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
//        $this->archivage->postPersist($entity); //TODO génrère un bug lors création sortie
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCampus(Campus $campus): array
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->where('s.siteOrganisateur = :campus')
            ->setParameter('campus', $campus)
            ->leftJoin('s.siteOrganisateur', 'so')
            ->addSelect('so')
            ->leftJoin('s.etat', 'e')
            ->addSelect('e')
            ->leftJoin('s.participants', 'p')
            ->addSelect('p');
//            ->leftJoin('s.lieu', 'l')
//            ->addSelect('l')
//            ->leftJoin('l.ville', 'v')
//            ->addSelect('v');
        return $queryBuilder->getQuery()->getResult();


    }

    public function findDetailsSortie($i){
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.id = :i')
            ->setParameter('i', $i)
            ->leftJoin('s.participants','participants')
            ->addSelect('participants')
            ->leftJoin('s.lieu','lieu')
            ->addSelect('lieu')
            ->leftJoin('s.siteOrganisateur', 'campus')
            ->addSelect('campus');

//        $paginator = new Paginator($query);
//        return $paginator;

        return $qb->getQuery()->getResult();
    }


    // Fonctions pour la commande symfony console app:trip:cleanup -------------------------------------------------
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function countOldRejected(): int{
        return $this->getOldRejectedQueryBuilder()->select('COUNT(s.id)')->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws Exception
     */
    public function deleteOldRejected(): int{
        return $this->getOldRejectedQueryBuilder()->delete()->getQuery()->execute();
    }

    /**
     * @throws Exception
     */
    private function getOldRejectedQueryBuilder(): QueryBuilder{
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut < :date')
            ->setParameters([
                'date' =>  new \DateTimeImmutable(-self::DAYS_BEFORE_REMOVAL.' days'),
            ])
        ;
    }

    // Fonctions pour la commande symfony console app:trip:update ------------------------------------------
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */

    public function sortiesACloturer(): array |bool
    {
        $sorties = $this->findAll();

        $etatCloture = $this->etatRepository->find(3);

            for ($i = 0; $i <= sizeof($sorties) - 1; $i++) {
                $nbParticipants = $sorties[$i]->getParticipants()->count();
                $nbPlace = $sorties[$i]->getNbInscriptionMax();
                $nbPlacesLibres = $nbPlace - $nbParticipants;
                $dateFinInscription =$sorties[$i]->getDateLimiteInscription();
                if ($nbPlacesLibres <= 0 || $dateFinInscription < new \DateTimeImmutable('now')) {
                    $sorties[$i]->setEtat($etatCloture);
                    $this->add($sorties[$i], true);
                }
            }
        return $sorties;
    }

    public function sortiesPasse(): array | bool
    {
        $sorties = $this->findNonAnnulee();
        $etatPasse = $this->etatRepository->find(5);
        $c = 0;
        for ($i = 0; $i <= sizeof($sorties) - 1; $i++) {
            $dateDebut = $sorties[$i]->getDateHeureDebut();
            if ($dateDebut < new \DateTimeImmutable('now')) {
                $sorties[$i]->setEtat($etatPasse);
                $this->add($sorties[$i], true);
                $c++;
            }
        }
        return $sorties;
    }

    public function sortiesEnCours(): array |bool
    {
        $sorties = $this->findAll();

        $etatPasse = $this->etatRepository->find(5);
        $c = 0;
        foreach ($sorties as $sortie) {
            for ($i = 0; $i <= sizeof($sorties) - 1; $i++) {
                $dateDebut = $sortie->getDateHeureDebut();
                if ($dateDebut == new \DateTimeImmutable('now')) {
                    $sorties[$i]->setEtat($etatPasse);
                    $this->add($sorties[$i], true);
                    $c++;
                }
            }
        }
        return $sorties;
    }

    private function findNonAnnulee()
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.etat != :etat')
            ->setParameters(['etat' => 'Annulee'])
        ;
        return $qb->getQuery()->getResult();
    }

    public function findByRequest(array $params): array
    {
        $qb = $this->createQueryBuilder('s') // TODO restreindre les trucs auxquels j'ai besoin
            ->leftJoin('s.siteOrganisateur', 'so')
            ->addSelect('so')
            ->leftJoin('s.etat', 'e')
            ->addSelect('e')
            ->leftJoin('s.participants', 'p')
            ->addSelect('p')
//            ->select('s.id as idSortie, e.libelle as libelleEtat, s.siteOrganisateur as siteOrganisateur, s.organisateur os organisateur, s.nom, s.dateHeureDebut, s.dateLimiteInscription, s.nbInscriptionMax')
            ->where('s.dateHeureDebut >= :today')->setParameter('today', new \DateTime());
        if (isset($params['sortiesPassees'])) {
            $qb->orWhere('s.dateHeureDebut <= :today')->setParameter('today', new \DateTime());
        }
        if ($params['nomSortieContient'] !== '') {
            $qb->andWhere('LOWER(s.nom) LIKE :portion')->setParameter('portion', '%'.strtolower($params['nomSortieContient']).'%');
        }
        if ($params['dateMin'] !== '') {
            $qb->andWhere('s.dateHeureDebut >= :dateMin')->setParameter('dateMin', $params['dateMin']);
        }
        if ($params['dateMax'] !== '') {
            $qb->andWhere('s.dateHeureDebut <= :dateMax')->setParameter('dateMax', $params['dateMax']);
        }

        if (isset($params['organisateurTrue'])) {
            if (!isset($params['inscritTrue']) && !isset($params['inscritFalse'])) {
                $qb->andWhere('s.organisateur = :user')->setParameter('user', $params['user']); // TODO il en manque une avec le bot 16 !!!
            } elseif (isset($params['inscritTrue']) && !isset($params['inscritFalse'])) {
                $qb->orWhere('s.organisateur = :user')->setParameter('user', $params['user']);
                $qb->andWhere(':user IN (p)')->setParameter('user', $params['user']);
            } elseif (!isset($params['inscritTrue']) && isset($params['inscritFalse'])) {
                $qb->andWhere('s.organisateur = :user')->setParameter('user', $params['user']);
                $qb->orWhere(':user NOT IN (p)')->setParameter('user', $params['user']);
                // TODO n'affiche pas les sorties où je suis organisateur parce que je suis également participant
            }
        } else {
            if (isset($params['inscritTrue']) && isset($params['inscritFalse'])) {
                $qb->andWhere('s.organisateur != :user')->setParameter('user', $params['user']);
            } elseif (isset($params['inscritTrue']) && !isset($params['inscritFalse'])) {
                $qb->andWhere('s.organisateur != :user')->setParameter('user', $params['user']);
                $qb->andWhere(':user IN (p)')->setParameter('user', $params['user']);
            } elseif (!isset($params['inscritTrue']) && isset($params['inscritFalse'])) {
                $qb->andWhere('s.organisateur != :user')->setParameter('user', $params['user']);
                $qb->andWhere(':user NOT IN (p)')->setParameter('user', $params['user']);
            }
        }



        $qb->andwhere('s.siteOrganisateur = :camp')->setParameter('camp', $params['campus'])
            ->orderBy('s.dateLimiteInscription', 'ASC');

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return SortieFixtures[] Returns an array of SortieFixtures objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SortieFixtures
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



}
