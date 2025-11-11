<?php

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Language>
 */
class LanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }

    //    /**
    //     * @return Language[] Returns an array of Language objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Language
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale, ?int $curriculum = null): array
    {
        $req = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale);

        if ($curriculum) {
            $req->andWhere('l.curriculum = :curriculumId')
                ->setParameter('curriculumId', $curriculum);
        }

        return $req->getQuery()->getResult();
    }

    public function findOneWithLocale(int $language, int $locale): Language
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->andWhere('l.id = :languageId')
            ->setParameter('languageId', $language)
            ->leftJoin('l.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
