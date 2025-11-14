<?php

namespace App\Controller;

use App\Entity\Education;
use App\Repository\EducationRepository;
use App\Serializer\EducationSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/education', name: 'education')]
class EducationController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly EducationSerializer $educationSerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, EducationRepository $educationRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $educationRepository->findAllWithLocale($lang->getId());
        $serializer = $this->educationSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Education $education, LocaleRequestService $localeRequestService, EducationRepository $educationRepository): JsonResponse
    {
        if (!$education) {
            throw new Exception('Education not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $educationRepository->findOneWithLocale($education->getId(), $lang->getId());
        $serializer = $this->educationSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}