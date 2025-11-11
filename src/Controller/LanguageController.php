<?php

namespace App\Controller;

use App\Entity\Language;
use App\Repository\LanguageRepository;
use App\Serializer\LanguageSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/language', name: 'language')]
class LanguageController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

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

        $lang = $localeRequestService->getLocale($request);
        $data = $languageRepository->findOneWithLocale($language->getId(), $lang->getId());
        $serializer = LanguageSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}