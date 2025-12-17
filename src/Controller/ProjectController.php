<?php

namespace App\Controller;

use App\DTO\ProjectDTO;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Serializer\ProjectSerializer;
use App\Service\ProjectService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project', name: 'project')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly ProjectSerializer $projectSerializer,
        private readonly ProjectService $projectService,
        private readonly ProjectRepository $projectRepository
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->projectRepository->findAllWithLocale($lang->getId());
        $serializer = $this->projectSerializer->list($data, 'list', $lang->getId());

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Project $project, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$project) {
            throw new Exception('Project not found.');
        }

        $isFromForm = filter_var($request->query->get('fromForm'), FILTER_VALIDATE_BOOL);
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $isFromForm ?
            $this->projectRepository->findOneById($project->getId()) :
            $this->projectRepository->findOneWithLocale($project->getId(), $lang->getId());
        $serializer = $this->projectSerializer->details(
            $data,
            $isFromForm,
            'details',
            $isFromForm ? null : $lang->getId(),
        );

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'],
            acceptFormat: 'json'
        )] ProjectDTO $dto
    ): JsonResponse {
        $project = $this->projectService->create($dto);
        $serializer = $this->projectSerializer->create($project);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Project successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'],
            acceptFormat: 'json'
        )] ProjectDTO $dto,
        Project $project
    ): JsonResponse {
        if (!$project) {
            throw new Exception('Project not found.');
        }

        $projectService = $this->projectService->update($project, $dto);
        $serializer = $this->projectSerializer->update($projectService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Project successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Project $project): JsonResponse
    {
        if (!$project) {
            throw new Exception('Project not found.');
        }

        $this->projectService->delete($project);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Project successfully deleted.']);
    }
}
