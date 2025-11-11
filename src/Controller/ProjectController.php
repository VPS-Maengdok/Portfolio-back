<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Serializer\ProjectSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project', name: 'project')]
class ProjectController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, ProjectRepository $projectRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $projectRepository->findAllWithLocale($lang->getId());
        $serializer = ProjectSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Project $Project, LocaleRequestService $localeRequestService, ProjectRepository $projectRepository): JsonResponse
    {
        if (!$Project) {
            throw new Exception('Project not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $projectRepository->findOneWithLocale($Project->getId(), $lang->getId());
        $serializer = ProjectSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}