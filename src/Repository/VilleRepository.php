<?php

namespace App\Repository;

use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Ville>
 *
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function add(Ville $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ville $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByRequest(array $params): Query
    {
        $qb = $this->createQueryBuilder('v')
            ->orderBy('v.codePostal', 'ASC');
        if ($params['portion'] !== '') {
            $qb->where('LOWER(v.nom) LIKE :portion')->setParameter('portion', '%'.strtolower($params['portion']).'%');
        }
        return $qb->getQuery();
    }

    public function findAllOrderByName()
    {
        $qb = $this->createQueryBuilder('v')
            ->orderBy('v.nom');
        return $qb->getQuery()->getResult();
    }
}
