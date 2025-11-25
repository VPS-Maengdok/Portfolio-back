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
        $qb = $this->createQueryBuilder('p')
            ->select(
                'DISTINCT p',
                'i18n','loc',
                'school','school_country','school_country_i18n',
                'company','company_country','company_country_i18n',
                'skill','skill_i18n',
                'tag','tag_i18n',
                'status','status_i18n',
                'technology','picture'
            )
    
            ->leftJoin('p.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'loc')
            ->andWhere('loc.id = :localeId')
    
            ->leftJoin('p.school', 'school')
            ->leftJoin('school.country', 'school_country')
            ->leftJoin('school_country.i18n', 'school_country_i18n', 'WITH', 'school_country_i18n.locale = :localeId')
    
            ->leftJoin('p.company', 'company')
            ->leftJoin('company.country', 'company_country')
            ->leftJoin('company_country.i18n', 'company_country_i18n', 'WITH', 'company_country_i18n.locale = :localeId')
    
            ->leftJoin('p.skill', 'skill')
            ->leftJoin('skill.i18n', 'skill_i18n', 'WITH', 'skill_i18n.locale = :localeId')
    
            ->leftJoin('p.tag', 'tag')
            ->leftJoin('tag.i18n', 'tag_i18n', 'WITH', 'tag_i18n.locale = :localeId')
    
            ->leftJoin('p.status', 'status')
            ->leftJoin('status.i18n', 'status_i18n', 'WITH', 'status_i18n.locale = :localeId')
    
            ->leftJoin('p.technology', 'technology')
            ->leftJoin('p.picture', 'picture')
    
            ->setParameter('localeId', $locale)
            ->orderBy('p.creationDate', 'DESC');
    
        if ($curriculum) {
            $qb->andWhere('p.curriculum = :curriculumId')
               ->setParameter('curriculumId', $curriculum);
        }
        if ($limit && $limit > 0) {
            $qb->setMaxResults($limit);
        }
    
        return $qb->getQuery()->getResult();
    }

    public function findOneWithLocale(int $project, int $locale): ?Project
    {
        return $this->createQueryBuilder('p')
            ->select(
                'DISTINCT p',
                'i18n','loc',
                'school','school_country','school_country_i18n',
                'company','company_country','company_country_i18n',
                'skill','skill_i18n',
                'tag','tag_i18n',
                'status','status_i18n',
                'technology','picture'
            )
            ->andWhere('p.id = :id')
            ->setParameter('id', $project)

            ->leftJoin('p.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'loc')
            ->andWhere('loc.id = :localeId')

            ->leftJoin('p.school', 'school')
            ->leftJoin('school.country', 'school_country')
            ->leftJoin('school_country.i18n', 'school_country_i18n', 'WITH', 'school_country_i18n.locale = :localeId')

            ->leftJoin('p.company', 'company')
            ->leftJoin('company.country', 'company_country')
            ->leftJoin('company_country.i18n', 'company_country_i18n', 'WITH', 'company_country_i18n.locale = :localeId')

            ->leftJoin('p.skill', 'skill')
            ->leftJoin('skill.i18n', 'skill_i18n', 'WITH', 'skill_i18n.locale = :localeId')

            ->leftJoin('p.tag', 'tag')
            ->leftJoin('tag.i18n', 'tag_i18n', 'WITH', 'tag_i18n.locale = :localeId')

            ->leftJoin('p.status', 'status')
            ->leftJoin('status.i18n', 'status_i18n', 'WITH', 'status_i18n.locale = :localeId')

            ->leftJoin('p.technology', 'technology')
            ->leftJoin('p.picture', 'picture')

            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
