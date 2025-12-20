<?php

namespace App\Controller;

use App\DTO\CurriculumDTO;
use App\Entity\Curriculum;
use App\Repository\CountryRepository;
use App\Repository\CurriculumRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\LanguageRepository;
use App\Repository\LinkRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use App\Repository\WorkTypeRepository;
use App\Serializer\CurriculumSerializer;
use App\Service\CurriculumService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use App\Service\Shared\PdfGenerator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/curriculum', name: 'curriculum')]
class CurriculumController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly CountryRepository $countryRepository,
        private readonly ProjectRepository $projectRepository,
        private readonly EducationRepository $educationRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly SkillRepository $skillRepository,
        private readonly WorkTypeRepository $workTypeRepository,
        private readonly LanguageRepository $languageRepository,
        private readonly TechnologyRepository $technologyRepository,
        private readonly LinkRepository $linkRepository,
        private readonly CurriculumSerializer $curriculumSerializer,
        private readonly CurriculumService $curriculumService,
        private readonly CurriculumRepository $curriculumRepository
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->curriculumRepository->findAllWithLocale($lang->getId());
        $collections = [];

        foreach ($data as $cv) {
            $collections[$cv->getId()] = $this->fetchCollections($cv->getId(), $lang->getId());
        }

        $serializer = $this->curriculumSerializer->list(
            $data,
            $collections,
            $lang->getId(),
        );

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Curriculum $curriculum, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$curriculum) {
            throw new Exception('Curriculum not found.');
        }

        $isFromForm = filter_var($request->query->get('fromForm'), FILTER_VALIDATE_BOOL);
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $limit = filter_var($request->query->get('limit'), FILTER_VALIDATE_INT) ?: null;

        $data = $isFromForm ?
            $this->curriculumRepository->findOneById($curriculum->getId()) :
            $this->curriculumRepository->findOneWithLocale($curriculum->getId(), $lang->getId());
        $collections = $this->fetchCollections($curriculum->getId(), $lang->getId(), $limit);

        $serializer = $this->curriculumSerializer->details(
            $data,
            $collections,
            $isFromForm,
            $lang->getId(),
        );

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'],
            acceptFormat: 'json'
        )] CurriculumDTO $dto
    ): JsonResponse {
        $curriculum = $this->curriculumService->create($dto);
        $serializer = $this->curriculumSerializer->create($curriculum);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Curriculum successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'],
            acceptFormat: 'json'
        )] CurriculumDTO $dto,
        Curriculum $curriculum
    ): JsonResponse {
        if (!$curriculum) {
            throw new Exception('Curriculum not found.');
        }

        $curriculumService = $this->curriculumService->update($curriculum, $dto);
        $serializer = $this->curriculumSerializer->update($curriculumService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Curriculum successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Curriculum $curriculum): JsonResponse
    {
        if (!$curriculum) {
            throw new Exception('Curriculum not found.');
        }

        $this->curriculumService->delete($curriculum);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Curriculum successfully deleted.']);
    }

    #[Route('/first', name: '_first', methods: ['GET'])]
    public function detailsFirstCurriculum(Request $request,  LocaleRequestService $localeRequestService): JsonResponse
    {
        $lang = $localeRequestService->getLocaleFromRequest($request);

        $curriculum = $this->curriculumRepository->findFirstCurriculum($lang->getId());
        $collections = $this->fetchCollections($curriculum->getId(), $lang->getId());
        $serializer = $this->curriculumSerializer->details(
            $curriculum,
            $collections,
            false,
            $lang->getId(),
        );
        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/pdf/{id}', name: '_pdf', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function previewPDF(Curriculum $curriculum, Request $request, LocaleRequestService $localeRequestService, PdfGenerator $generator): Response
    {
        $disposition = $request->query->get('download') === '1'
            ? 'attachment'
            : 'inline';
        $lang = $localeRequestService->getLocaleFromRequest($request);
        $limit = filter_var($request->query->get('limit'), FILTER_VALIDATE_INT) ?: null;

        $data = $this->curriculumRepository->findOneWithLocale($curriculum->getId(), $lang->getId());
        $collections = $this->fetchCollections($curriculum->getId(), $lang->getId(), $limit);
        $pdf = $generator->generate($data, $collections, $lang->getShortened());

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf(
                '%s; filename="%s %s resume %s.pdf"',
                $disposition,
                $data->getFirstname(),
                $data->getLastname(),
                $lang->getShortened()
            ),
        ]);
    }


    private function fetchCollections(int $curriculum, int $locale, ?int $limit = null): array
    {
        return [
            'visa' => $this->countryRepository->findAllWithLocale($locale, $curriculum),
            'expectedCountry' => $this->countryRepository->findAllWithLocale($locale, $curriculum, true),
            'project' => $this->projectRepository->findAllWithLocale($locale, $curriculum, $limit),
            'education' => $this->educationRepository->findAllWithLocale($locale, $curriculum, $limit),
            'experience' => $this->experienceRepository->findAllWithLocale($locale, $curriculum, $limit),
            'skill' => $this->skillRepository->findAllWithLocale($locale, $curriculum),
            'workType' => $this->workTypeRepository->findAllWithLocale($locale, $curriculum),
            'language' => $this->languageRepository->findAllWithLocale($locale, $curriculum),
            'technology' => $this->technologyRepository->findAllForCurriculum($curriculum),
            'link' => $this->linkRepository->findAllWithLocale($locale, $curriculum),
        ];
    }
}
