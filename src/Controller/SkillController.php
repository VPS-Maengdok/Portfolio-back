<?php

namespace App\Controller;

use App\DTO\SkillDTO;
use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\Serializer\SkillSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use App\Service\SkillService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/skill', name: 'skill')]
class SkillController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly SkillRepository $skillRepository,
        private readonly SkillService $skillService,
        private readonly SkillSerializer $skillSerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->skillRepository->findAllWithLocale($lang->getId());
        $serializer = $this->skillSerializer->list($data, $lang->getId());

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Skill $skill, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$skill) {
            throw new Exception('Skill not found.');
        }

        $isFromForm = filter_var($request->query->get('fromForm'), FILTER_VALIDATE_BOOL);
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $isFromForm ?
            $this->skillRepository->findOneById($skill->getId()) :
            $this->skillRepository->findOneWithLocale($skill->getId(), $lang->getId());
        $serializer = $this->skillSerializer->details(
            $data,
            $isFromForm,
            $isFromForm ? null : $lang->getId(),
        );

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'],
            acceptFormat: 'json'
        )] SkillDTO $dto
    ): JsonResponse {
        $skill = $this->skillService->create($dto);
        $serializer = $this->skillSerializer->create($skill);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Skill successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'],
            acceptFormat: 'json'
        )] SkillDTO $dto,
        Skill $skill
    ): JsonResponse {
        if (!$skill) {
            throw new Exception('Skill not found.');
        }

        $skillService = $this->skillService->update($skill, $dto);
        $serializer = $this->skillSerializer->update($skillService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Skill successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Skill $skill): JsonResponse
    {
        if (!$skill) {
            throw new Exception('Skill not found.');
        }

        $this->skillService->delete($skill);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Skill successfully deleted.']);
    }
}
