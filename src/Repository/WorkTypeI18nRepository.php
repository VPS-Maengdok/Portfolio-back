<?php

namespace App\Repository;

use App\Entity\WorkTypeI18n;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkTypeI18n>
 */
class WorkTypeI18nRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTypeI18n::class);
    }

    //    /**
    //     * @return WorkTypeI18n[] Returns an array of WorkTypeI18n objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WorkTypeI18n
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
