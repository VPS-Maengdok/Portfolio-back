<?php

namespace App\Controller;

use App\DTO\ExperienceDTO;
use App\Entity\Experience;
use App\Repository\ExperienceRepository;
use App\Serializer\ExperienceSerializer;
use App\Service\ExperienceService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/experience', name: 'experience')]
class ExperienceController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly ExperienceSerializer $experienceSerializer,
        private readonly ExperienceRepository $experienceRepository,
        private readonly ExperienceService $experienceService
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->experienceRepository->findAllWithLocale($lang->getId());
        $serializer = $this->experienceSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Experience $experience, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$experience) {
            throw new Exception('Experience not found.');
        }

        $isFromForm = filter_var($request->query->get('fromForm'), FILTER_VALIDATE_BOOL);
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $isFromForm ?
            $this->experienceRepository->findOneById($experience->getId()) :
            $this->experienceRepository->findOneWithLocale($experience->getId(), $lang->getId());
        $serializer = $this->experienceSerializer->details($data, $isFromForm, $isFromForm ? null : $lang->getId());

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'],
            acceptFormat: 'json'
        )] ExperienceDTO $dto
    ): JsonResponse {
        $experience = $this->experienceService->create($dto);
        $serializer = $this->experienceSerializer->create($experience);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Experience successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'],
            acceptFormat: 'json'
        )] ExperienceDTO $dto,
        Experience $experience
    ): JsonResponse {
        if (!$experience) {
            throw new Exception('Experience not found.');
        }

        $experienceService = $this->experienceService->update($experience, $dto);
        $serializer = $this->experienceSerializer->update($experienceService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Experience successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Experience $experience): JsonResponse
    {
        if (!$experience) {
            throw new Exception('Experience not found.');
        }

        $this->experienceService->delete($experience);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Experience successfully deleted.']);
    }
}
