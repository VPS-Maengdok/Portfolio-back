<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    //    /**
    //     * @return Tag[] Returns an array of Tag objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tag
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale): array
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->leftJoin('t.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getResult();
    }

    public function findOneWithLocale(int $tag, int $locale): Tag
    {
        return $this->createQueryBuilder('t')
        ->select('t')
        ->andWhere('t.id = :tagId')
        ->setParameter('tagId', $tag)
        ->leftJoin('t.i18n', 'i18n')
        ->addSelect('i18n')
        ->leftJoin('i18n.locale', 'locale')
        ->addSelect('locale')
        ->andWhere('locale.id = :localeId')
        ->setParameter('localeId', $locale)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
