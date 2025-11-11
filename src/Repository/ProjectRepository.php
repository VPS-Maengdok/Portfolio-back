<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale, ?int $curriculum = null, ?int $limit = null): array
    {
        $req = $this->createQueryBuilder('p')
            ->select(
                'DISTINCT p',
                'i18n',
                'locale',
                'school',
                'school_country',
                'school_country_i18n',
                'company',
                'company_country',
                'company_country_i18n', 
                'skill',
                'skill_i18n',
                'skill_locale',
                'tag',
                'tag_i18n',
                'tag_locale',
                'status',
                'status_i18n',
                'status_locale',
                'technology',
                'picture'
            )

            ->leftJoin('p.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :localeId')

            ->leftJoin('p.school', 'school')
            ->leftJoin('school.country', 'school_country')
            ->leftJoin(
                'school_country.i18n',
                'school_country_i18n',
                'WITH',
                'EXISTS (
                    SELECT scl.id FROM App\Entity\Locale AS scl 
                    WHERE scl MEMBER OF school_country_i18n.locale AND scl.id = :localeId
                )'
            )

            ->leftJoin('p.company', 'company')
            ->leftJoin('company.country', 'company_country')
            ->leftJoin(
                'company_country.i18n',
                'company_country_i18n',
                'WITH',
                'EXISTS (
                    SELECT ccl.id FROM App\Entity\Locale AS ccl
                    WHERE ccl MEMBER OF company_country_i18n.locale AND ccl.id = :localeId
                )'
            )

            ->leftJoin('p.skill', 'skill')
            ->leftJoin('skill.i18n', 'skill_i18n')
            ->leftJoin('skill_i18n.locale', 'skill_locale')
            ->andWhere('skill_locale.id = :localeId')

            ->leftJoin('p.tag', 'tag')
            ->leftJoin('tag.i18n', 'tag_i18n')
            ->leftJoin('tag_i18n.locale', 'tag_locale')
            ->andWhere('tag_locale.id = :localeId')

            ->leftJoin('p.status', 'status')
            ->leftJoin('status.i18n', 'status_i18n')
            ->leftJoin('status_i18n.locale', 'status_locale')
            ->andWhere('status_locale.id = :localeId')

            ->leftJoin('p.technology', 'technology')
            
            ->leftJoin('p.picture', 'picture')

            ->setParameter('localeId', $locale);

        if ($curriculum) {
            $req->andWhere('p.curriculum = :curriculumId')
                ->setParameter('curriculumId', $curriculum);
        }

        $req->orderBy('p.creationDate', 'DESC');

        if ($limit && $limit > 0) {
            $req->setMaxResults($limit);
        }

        return $req->getQuery()->getResult();
    }

    public function findOneWithLocale(int $project, int $locale): Project
    {
        return $this->createQueryBuilder('p')
            ->select(
                'DISTINCT p',
                'i18n',
                'locale',
                'school',
                'school_country',
                'school_country_i18n',
                'company',
                'company_country',
                'company_country_i18n', 
                'skill',
                'skill_i18n',
                'skill_locale',
                'tag',
                'tag_i18n',
                'tag_locale',
                'status',
                'status_i18n',
                'status_locale',
                'technology',
                'picture'
            )
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $project)

            ->leftJoin('p.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :localeId')

            ->leftJoin('p.school', 'school')
            ->leftJoin('school.country', 'school_country')
            ->leftJoin(
                'school_country.i18n',
                'school_country_i18n',
                'WITH',
                'EXISTS (
                    SELECT scl.id FROM App\Entity\Locale AS scl 
                    WHERE scl MEMBER OF school_country_i18n.locale AND scl.id = :localeId
                )'
            )

            ->leftJoin('p.company', 'company')
            ->leftJoin('company.country', 'company_country')
            ->leftJoin(
                'company_country.i18n',
                'company_country_i18n',
                'WITH',
                'EXISTS (
                    SELECT ccl.id FROM App\Entity\Locale AS ccl
                    WHERE ccl MEMBER OF company_country_i18n.locale AND ccl.id = :localeId
                )'
            )

            ->leftJoin('p.skill', 'skill')
            ->leftJoin('skill.i18n', 'skill_i18n')
            ->leftJoin('skill_i18n.locale', 'skill_locale')
            ->andWhere('skill_locale.id = :localeId')

            ->leftJoin('p.tag', 'tag')
            ->leftJoin('tag.i18n', 'tag_i18n')
            ->leftJoin('tag_i18n.locale', 'tag_locale')
            ->andWhere('tag_locale.id = :localeId')

            ->leftJoin('p.status', 'status')
            ->leftJoin('status.i18n', 'status_i18n')
            ->leftJoin('status_i18n.locale', 'status_locale')
            ->andWhere('status_locale.id = :localeId')

            ->leftJoin('p.technology', 'technology')
            
            ->leftJoin('p.picture', 'picture')

            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
