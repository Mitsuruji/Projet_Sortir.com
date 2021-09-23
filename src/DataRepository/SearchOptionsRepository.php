<?php

namespace App\DataRepository;

use App\Entity\SearchOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchOptions[]    findAll()
 * @method SearchOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchOptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchOptions::class);
    }

    // /**
    //  * @return SearchOptions[] Returns an array of SearchOptions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SearchOptions
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
