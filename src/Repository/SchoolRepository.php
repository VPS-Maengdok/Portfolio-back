<?php

namespace App\Repository;

use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<School>
 */
class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    //    /**
    //     * @return School[] Returns an array of School objects
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

    //    public function findOneBySomeField($value): ?School
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
            ->leftJoin('s.country', 'c')
            ->addSelect('c')
            ->leftJoin('c.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getResult();
    }

    public function findOneWithLocale(int $school, int $locale): School
    {
        return $this->createQueryBuilder('s')
        ->select('s')
        ->andWhere('s.id = :schoolId')
        ->setParameter('schoolId', $school)
        ->leftJoin('s.country', 'c')
        ->addSelect('c')
        ->leftJoin('c.i18n', 'i18n')
        ->addSelect('i18n')
        ->leftJoin('i18n.locale', 'locale')
        ->addSelect('locale')
        ->andWhere('locale.id = :localeId')
        ->setParameter('localeId', $locale)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
