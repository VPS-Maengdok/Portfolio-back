<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\Serializer\SkillSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/skill', name: 'skill')]
class SkillController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, SkillRepository $skillRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $skillRepository->findAllWithLocale($lang->getId());
        $serializer = SkillSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Skill $skill, LocaleRequestService $localeRequestService, SkillRepository $skillRepository): JsonResponse
    {
        if (!$skill) {
            throw new Exception('Skill not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $skillRepository->findOneWithLocale($skill->getId(), $lang->getId());
        $serializer = SkillSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}