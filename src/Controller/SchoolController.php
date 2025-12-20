<?php

namespace App\Controller;

use App\DTO\SchoolDTO;
use App\Entity\School;
use App\Repository\SchoolRepository;
use App\Serializer\SchoolSerializer;
use App\Service\SchoolService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/school', name: 'school')]
class SchoolController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly SchoolSerializer $schoolSerializer,
        private readonly SchoolRepository $schoolRepository,
        private readonly SchoolService $schoolService
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->schoolRepository->findAllWithLocale($lang->getId());
        $serializer = $this->schoolSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, School $school, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$school) {
            throw new Exception('School not found.');
        }

        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $this->schoolRepository->findOneWithLocale($school->getId(), $lang->getId());
        $serializer = $this->schoolSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'],
            acceptFormat: 'json'
        )] SchoolDTO $dto
    ): JsonResponse {
        $school = $this->schoolService->create($dto);
        $serializer = $this->schoolSerializer->create($school);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'School successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'],
            acceptFormat: 'json'
        )] SchoolDTO $dto,
        School $school
    ): JsonResponse {
        if (!$school) {
            throw new Exception('School not found.');
        }

        $schoolService = $this->schoolService->update($school, $dto);
        $serializer = $this->schoolSerializer->update($schoolService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'School successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(School $school): JsonResponse
    {
        if (!$school) {
            throw new Exception('School not found.');
        }

        $this->schoolService->delete($school);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'School successfully deleted.']);
    }
}
