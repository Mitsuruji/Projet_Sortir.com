<?php

namespace App\Repository;

use App\Entity\SortieHistorisee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SortieHistorisee|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortieHistorisee|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortieHistorisee[]    findAll()
 * @method SortieHistorisee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieHistoriseeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortieHistorisee::class);
    }

    // /**
    //  * @return SortieHistorisee[] Returns an array of SortieHistorisee objects
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
    public function findOneBySomeField($value): ?SortieHistorisee
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
