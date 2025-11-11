<?php

namespace App\Repository;

use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Status>
 */
class StatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    //    /**
    //     * @return Status[] Returns an array of Status objects
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

    //    public function findOneBySomeField($value): ?Status
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale): array
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->leftJoin('s.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getResult();
    }

    public function findOneWithLocale(int $status, int $locale): Status
    {
        return $this->createQueryBuilder('s')
        ->select('s')
        ->andWhere('s.id = :statusId')
        ->setParameter('statusId', $status)
        ->leftJoin('s.i18n', 'i18n')
        ->addSelect('i18n')
        ->leftJoin('i18n.locale', 'locale')
        ->addSelect('locale')
        ->andWhere('locale.id = :localeId')
        ->setParameter('localeId', $locale)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
