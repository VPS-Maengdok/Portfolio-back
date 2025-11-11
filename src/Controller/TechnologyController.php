<?php

namespace App\Controller;

use App\Entity\Technology;
use App\Repository\TechnologyRepository;
use App\Serializer\TechnologySerializer;
use App\Service\Shared\ApiResponseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/technology', name: 'technology')]
class TechnologyController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(TechnologyRepository $technologyRepository): JsonResponse
    {
        $data = $technologyRepository->findAll();
        $serializer = TechnologySerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Technology $technology): JsonResponse
    {
        if (!$technology) {
            throw new Exception('Technology not found.');
        }

        $serializer = TechnologySerializer::details($technology);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}