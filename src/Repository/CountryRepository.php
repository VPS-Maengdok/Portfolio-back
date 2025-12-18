<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    //    /**
    //     * @return Country[] Returns an array of Country objects
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

    //    public function findOneBySomeField($value): ?Country
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithLocale(int $locale, ?int $curriculum = null, ?bool $isForExpectedCountries = false): array
    {
        $req = $this->createQueryBuilder('c')
            ->select('c', 'i18n', 'locale')
            ->leftJoin('c.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale);

        if ($curriculum) {
            $req->setParameter('curriculumId', $curriculum);
            if ($isForExpectedCountries) {
                $req->andWhere('c.expectedCountry = :curriculumId');
            } else {
                $req->andWhere('c.visaAvailability = :curriculumId');
            }
        }

        return $req->getQuery()->getResult();
    }

    public function findOneWithLocale(int $country, int $locale): Country
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'i18n', 'locale')
            ->andWhere('c.id = :countryId')
            ->setParameter('countryId', $country)
            ->leftJoin('c.i18n', 'i18n')
            ->leftJoin('i18n.locale', 'locale')
            ->andWhere('locale.id = :localeId')
            ->setParameter('localeId', $locale)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
