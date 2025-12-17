<?php

namespace App\Security;

use App\Entity\AccessToken;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        $accessToken = new AccessToken();
        $accessToken->setToken(bin2hex(random_bytes(32)));
        $accessToken->setUser($user);
        $accessToken->setExpiresAt((new DateTimeImmutable())->modify('+1 month'));

        $this->em->persist($accessToken);
        $this->em->flush();

        return new JsonResponse([
            'token' => $accessToken->getToken(),
            'expiresAt' => $accessToken->getExpiresAt()->format(DATE_ATOM),
        ]);
    }
}
