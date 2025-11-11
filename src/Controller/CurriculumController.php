<?php

namespace App\Controller;

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
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        private readonly LinkRepository $linkRepository
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, CurriculumRepository $curriculumRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $curriculumRepository->findAllWithLocale($lang->getId());
        $collections = [];

        foreach ($data as $cv) {
            $collections[$cv->getId()] = $this->fetchCollections($cv->getId(), $lang->getId());
        }
    
        $serializer = CurriculumSerializer::list($data, $collections);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Curriculum $curriculum, LocaleRequestService $localeRequestService, CurriculumRepository $curriculumRepository): JsonResponse
    {
        if (!$curriculum) {
            throw new Exception('Curriculum not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $limit = filter_var($request->query->get('limit'), FILTER_VALIDATE_INT) ?: null;
    
        $data = $curriculumRepository->findOneWithLocale($curriculum->getId(), $lang->getId());
        $collections = $this->fetchCollections($curriculum->getId(), $lang->getId(), $limit);

        $serializer = CurriculumSerializer::details($data, $collections);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
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