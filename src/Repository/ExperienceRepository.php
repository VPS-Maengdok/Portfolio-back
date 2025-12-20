<?php

namespace App\Repository;

use App\Entity\Experience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Experience>
 */
class ExperienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }

    //    /**
    //     * @return Experience[] Returns an array of Experience objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Experience
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale, ?int $curriculum = null, ?int $limit = null): array
    {
        $req = $this->createQueryBuilder('e')
            ->select('DISTINCT e', 'i18n', 'locale', 'company', 'company_country', 'country_i18n', 'country_locale', 'skill', 'skill_i18n', 'skill_locale', 'technology')

            ->leftJoin('e.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :localeId')

            ->leftJoin('e.company', 'company')
            ->leftJoin('company.country', 'company_country')
            ->leftJoin('company_country.i18n', 'country_i18n')
            ->leftJoin('country_i18n.locale', 'country_locale')
            ->andWhere('country_locale.id = :localeId')

            ->leftJoin('e.skill', 'skill')
            ->leftJoin('skill.i18n', 'skill_i18n')
            ->leftJoin('skill_i18n.locale', 'skill_locale')
            ->andWhere('skill_locale.id = :localeId')

            ->leftJoin('e.technology', 'technology')

            ->setParameter('localeId', $locale);

        if ($curriculum) {
            $req->andWhere('e.curriculum = :curriculumId')
                ->setParameter('curriculumId', $curriculum);
        }

        $req
            ->addOrderBy('CASE WHEN e.endingDate IS NULL THEN 1 ELSE 0 END', 'DESC')
            ->addOrderBy('e.endingDate', 'DESC')
            ->addOrderBy('e.startingDate', 'DESC');

        if ($limit && $limit > 0) {
            $req->setMaxResults($limit);
        }

        return $req->getQuery()->getResult();
    }

    public function findOneWithLocale(int $experience, int $locale): Experience
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e', 'i18n', 'locale', 'company', 'company_country', 'country_i18n', 'country_locale', 'skill', 'skill_i18n', 'skill_locale', 'technology')
            ->andWhere('e.id = :experienceId')
            ->setParameter('experienceId', $experience)

            ->leftJoin('e.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :localeId')

            ->leftJoin('e.company', 'company')
            ->leftJoin('company.country', 'company_country')
            ->leftJoin('company_country.i18n', 'country_i18n')
            ->leftJoin('country_i18n.locale', 'country_locale')
            ->andWhere('country_locale.id = :localeId')

            ->leftJoin('e.skill', 'skill')
            ->leftJoin('skill.i18n', 'skill_i18n')
            ->leftJoin('skill_i18n.locale', 'skill_locale')
            ->andWhere('skill_locale.id = :localeId')

            ->leftJoin('e.technology', 'technology')

            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
