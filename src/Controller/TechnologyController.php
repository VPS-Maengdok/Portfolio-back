<?php

namespace App\Controller;

use App\DTO\TechnologyDTO;
use App\Entity\Technology;
use App\Repository\TechnologyRepository;
use App\Serializer\TechnologySerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\TechnologyService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/technology', name: 'technology')]
class TechnologyController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse, 
        private readonly TechnologyRepository $technologyRepository,
        private readonly TechnologyService $technologyService,
        private readonly TechnologySerializer $technologySerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $data = $this->technologyRepository->findAll();
        $serializer = $this->technologySerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Technology $technology): JsonResponse
    {
        if (!$technology) {
            throw new Exception('Technology not found.');
        }

        $serializer = $this->technologySerializer->details($technology);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] TechnologyDTO $dto
    ): JsonResponse
    {
        $technology = $this->technologyService->create($dto);
        $serializer = $this->technologySerializer->create($technology);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Technology successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] TechnologyDTO $dto, 
        Technology $technology
    ): JsonResponse
    {
        if (!$technology) {
            throw new Exception('Technology not found.');
        }

        $technologyService = $this->technologyService->update($technology, $dto);
        $serializer = $this->technologySerializer->update($technologyService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Technology successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Technology $technology): JsonResponse
    {
        if (!$technology) {
            throw new Exception('Technology not found.');
        }

        $this->technologyService->delete($technology);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Technology successfully deleted.']);
    }
}