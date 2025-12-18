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
            ->select(
                'DISTINCT c',
                'i18n',
                'locale',
                'locale_country',
                'locale_country_i18n'
            )

            ->leftJoin('c.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :loc')

            ->leftJoin('c.location', 'locale_country')
            ->leftJoin('locale_country.i18n', 'locale_country_i18n', 'WITH', 'locale_country_i18n.locale = :loc')

            ->setParameter('loc', $locale)
            ->getQuery()
            ->getResult();
    }

    public function findOneWithLocale(int $curriculum, int $locale, ?int $limit = null): ?Curriculum
    {
        return $this->createQueryBuilder('c')
            ->select(
                'DISTINCT c',
                'i18n',
                'locale',
                'locale_country',
                'locale_country_i18n'
            )
            ->andWhere('c.id = :id')

            ->leftJoin('c.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :loc')

            ->leftJoin('c.location', 'locale_country')
            ->leftJoin('locale_country.i18n', 'locale_country_i18n', 'WITH', 'locale_country_i18n.locale = :loc')

            ->setParameter('id', $curriculum)
            ->setParameter('loc', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFirstCurriculum(int $locale): ?Curriculum
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c', 'i18n', 'locale')
            ->leftJoin('c.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
