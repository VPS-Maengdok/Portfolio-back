<?php

namespace App\Security;

use App\Entity\AccessToken;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $token = $this->em->getRepository(AccessToken::class)
            ->findOneBy(['token' => $accessToken]);

        if (!$token || $token->getExpiresAt() < new DateTimeImmutable()) {
            throw new AuthenticationException('Invalid token');
        }

        $userIdentifier = $token->getUser()->getUserIdentifier();

        return new UserBadge($userIdentifier);
    }
}
