<?php

namespace App\Controller;

use App\Entity\School;
use App\Repository\SchoolRepository;
use App\Serializer\SchoolSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/school', name: 'school')]
class SchoolController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly SchoolSerializer $schoolSerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, SchoolRepository $schoolRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $schoolRepository->findAllWithLocale($lang->getId());
        $serializer = $this->schoolSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, School $school, LocaleRequestService $localeRequestService, SchoolRepository $schoolRepository): JsonResponse
    {
        if (!$school) {
            throw new Exception('School not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $schoolRepository->findOneWithLocale($school->getId(), $lang->getId());
        $serializer = $this->schoolSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}