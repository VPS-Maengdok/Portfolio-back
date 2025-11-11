<?php

namespace App\Repository;

use App\Entity\WorkType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkType>
 */
class WorkTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkType::class);
    }

    //    /**
    //     * @return WorkType[] Returns an array of WorkType objects
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

    //    public function findOneBySomeField($value): ?WorkType
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale, ?int $curriculum = null): array
    {
        $req = $this->createQueryBuilder('wt')
            ->select('wt')
            ->leftJoin('wt.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale);

        if ($curriculum) {
            $req->andWhere('wt.curriculum = :curriculumId')
                ->setParameter('curriculumId', $curriculum);
        }

        return $req->getQuery()->getResult();
    }

    public function findOneWithLocale(int $workType, int $locale): WorkType
    {
        return $this->createQueryBuilder('wt')
            ->select('wt')
            ->andWhere('wt.id = :workTypeId')
            ->setParameter('workTypeId', $workType)
            ->leftJoin('wt.i18n', 'i18n')
            ->addSelect('i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->addSelect('locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
