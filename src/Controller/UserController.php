<?php

namespace App\Controller;

use App\Repository\AccessTokenRepository;
use App\Repository\UserRepository;
use App\Serializer\UserSerializer;
use App\Service\Shared\ApiResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly UserSerializer $userSerializer,
        private readonly EntityManagerInterface $em,
        private readonly AccessTokenRepository $accessTokenRepository,
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(UserRepository $userRepository): JsonResponse
    {
        $data = $userRepository->findAll();
        $serializer = $this->userSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function getMyInfo(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            throw new UnauthorizedHttpException('You can\'t access this ressource.');
        }

        $serializer = $this->userSerializer->details($user);

        return $this->apiResponse->getApiResponse(200, data: $serializer);
    }

    #[Route('/login', name: '_login', methods: ['POST'])]
    public function login(): never
    {
        throw new \LogicException('This method is handled by the security firewall (json_login).');
    }

    #[Route('/disconnect', name: '_disconnect', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function disconnect(Request $request): JsonResponse
    {
        $user = $this->getUser();

        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('Missing or invalid Authorization header.');
        }

        $rawToken = trim(substr($authHeader, 7));

        $tokenEntity = $this->accessTokenRepository->findOneBy(['token' => $rawToken]);

        if (!$tokenEntity || $tokenEntity->getUser() !== $user) {
            throw new AuthenticationException('Invalid token.');
        }

        $this->em->remove($tokenEntity);
        $this->em->flush();

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Disconnected']);
    }
}