<?php

namespace App\Repository;

use App\Entity\Curriculum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Curriculum>
 */
class CurriculumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Curriculum::class);
    }

    //    /**
    //     * @return Curriculum[] Returns an array of Curriculum objects
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

    //    public function findOneBySomeField($value): ?Curriculum
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
        ->select('c', 'i18n')
        ->leftJoin('c.i18n', 'i18n')
        ->leftJoin('i18n.locale', 'locale')

        ->leftJoin('c.location', 'locale_country')
        ->leftJoin('locale_country.i18n', 'locale_country_i18n', 'WITH', 'EXISTS (
        SELECT l1.id FROM App\Entity\Locale l1
        WHERE l1 MEMBER OF locale_country_i18n.locale AND l1.id = :loc
        )')

        ->andWhere('locale.id = :loc')
        ->setParameter('loc', $locale)
        ->getQuery()
        ->getResult();
    }

    public function findOneWithLocale(int $curriculum, int $locale, ?int $limit = null): Curriculum
    {
        return $this->createQueryBuilder('c')
        ->select('c', 'i18n')
        ->leftJoin('c.i18n', 'i18n')
        ->leftJoin('i18n.locale', 'locale')

        ->leftJoin('c.location', 'locale_country')
        ->leftJoin('locale_country.i18n', 'locale_country_i18n', 'WITH', 'EXISTS (
        SELECT l1.id FROM App\Entity\Locale l1
        WHERE l1 MEMBER OF locale_country_i18n.locale AND l1.id = :loc
        )')

        ->andWhere('c.id = :id')
        ->andWhere('locale.id = :loc')
        ->setParameter('id', $curriculum)
        ->setParameter('loc', $locale)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
