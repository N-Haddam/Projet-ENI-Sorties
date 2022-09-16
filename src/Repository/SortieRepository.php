<?php

namespace App\Repository;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\EventListener\Archivage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
    private Archivage $archivage;

    public function __construct(ManagerRegistry $registry)
    {
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
    public function findContient($nomSortieContient){
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :nom')
            ->setParameter('nom', '%'.$nomSortieContient.'%')
//            ->andWhere('s.siteOrganisateur = :id')
//            ->setParameter('id', $campusId)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findFiltered($campusId, $dateMin, $dateMax, $organisateurId): array //$campusChoisi, $nomSortieContient, $dateMin, $dateMax, $organisateurTrue, $inscritTrue, $inscritFalse, $sortiesPassees
    {
        return $this->createQueryBuilder('s')
//            ->andWhere('s.etat LIKE Ouverte')
//            ->setParameter('Ouverte', $sortiesPassees)
//            ->andWhere('s.nom LIKE %nom%')
//            ->setParameter('nom', $nomSortieContient)
            ->andWhere('s.siteOrganisateur = :id')
            ->setParameter('id', $campusId)
            ->andWhere('s.dateHeureDebut >= :dateMin')
            ->setParameter('dateMin', $dateMin)
            ->andWhere('s.dateLimiteInscription<= :dateMax')
            ->setParameter('dateMax', $dateMax)
            ->andWhere('s.organisateur = :id')
            ->setParameter('id', $organisateurId)

            ->getQuery()
            ->getResult()
        ;
    }

    public function findDateFiltered($dateMin, $dateMax){
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut >= :dateMin')
            ->setParameter('dateMin', $dateMin)
            ->andWhere('s.dateLimiteInscription<= :dateMax')
            ->setParameter('dateMax', $dateMax)
            ->getQuery()
            ->getResult()
        ;
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


    // Fonctions pour la commande symfony console app:trip:cleanup
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
