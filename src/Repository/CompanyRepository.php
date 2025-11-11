<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    //    /**
    //     * @return Company[] Returns an array of Company objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Company
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale): array
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.country', 'country')
            ->addSelect('country')
            ->leftJoin('country.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getResult();
    }

    public function findOneWithLocale(int $company, int $locale): Company
    {
        return $this->createQueryBuilder('c')
        ->select('c')
        ->leftJoin('c.country', 'country')
        ->addSelect('country')
        ->andWhere('country.id = :countryId')
        ->setParameter('countryId', $company)
        ->leftJoin('country.i18n', 'i18n')
        ->addSelect('i18n')
        ->leftJoin('i18n.locale', 'locale')
        ->addSelect('locale')
        ->andWhere('locale.id = :localeId')
        ->setParameter('localeId', $locale)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
