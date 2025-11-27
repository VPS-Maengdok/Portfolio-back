<?php

namespace App\Repository;

use App\Entity\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessToken>
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    /**
     * Trouve un token par sa valeur brute (celle du header Authorization).
     */
    public function findValidToken(string $token): ?AccessToken
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
            ->andWhere('t.token = :token')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Supprime tous les tokens expirés.
     */
    public function removeExpiredTokens(): int
    {
        return $this->createQueryBuilder('t')
            ->delete()
            ->andWhere('t.expiresAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
