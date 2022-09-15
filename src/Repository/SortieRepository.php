<?php

namespace App\Repository;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

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
