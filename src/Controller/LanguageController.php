<?php

namespace App\Controller;

use App\DTO\LanguageDTO;
use App\Entity\Language;
use App\Repository\LanguageRepository;
use App\Serializer\LanguageSerializer;
use App\Service\LanguageService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/language', name: 'language')]
class LanguageController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly LanguageRepository $languageRepository,
        private readonly LanguageService $languageService
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, LanguageRepository $languageRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $languageRepository->findAllWithLocale($lang->getId());
        $serializer = LanguageSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Language $language, LocaleRequestService $localeRequestService, LanguageRepository $languageRepository): JsonResponse
    {
        if (!$language) {
            throw new Exception('Language not found.');
        }

        $query = filter_var($request->query->get('fromForm'), FILTER_VALIDATE_BOOL);
        $lang = $localeRequestService->getLocale($request);
        $data = $query ? 
        $this->languageRepository->findOneById($language->getId()) :
        $this->languageRepository->findOneWithLocale($language->getId(), $lang->getId());
        $serializer = LanguageSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] LanguageDTO $dto
    ): JsonResponse
    {
        $language = $this->languageService->create($dto);
        $serializer = LanguageSerializer::create($language);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Language successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] LanguageDTO $dto, 
        Language $language
    ): JsonResponse
    {
        if (!$language) {
            throw new Exception('Language not found.');
        }

        $languageService = $this->languageService->update($language->getId(), $dto);
        $serializer = LanguageSerializer::update($languageService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Language successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Language $language): JsonResponse
    {
        if (!$language) {
            throw new Exception('Language not found.');
        }

        $this->languageService->delete($language);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Language successfully deleted.']);
    }
}