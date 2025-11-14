<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Repository\ExperienceRepository;
use App\Serializer\ExperienceSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/experience', name: 'experience')]
class ExperienceController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly ExperienceSerializer $experienceSerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, ExperienceRepository $experienceRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $experienceRepository->findAllWithLocale($lang->getId());
        $serializer = $this->experienceSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Experience $experience, LocaleRequestService $localeRequestService, ExperienceRepository $experienceRepository): JsonResponse
    {
        if (!$experience) {
            throw new Exception('Experience not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $experienceRepository->findOneWithLocale($experience->getId(), $lang->getId());
        $serializer = $this->experienceSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}