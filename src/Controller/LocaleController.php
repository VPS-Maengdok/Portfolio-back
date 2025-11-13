<?php

namespace App\Controller;

use App\DTO\LocaleDTO;
use App\Entity\Locale;
use App\Repository\LocaleRepository;
use App\Serializer\LocaleSerializer;
use App\Service\LocaleService;
use App\Service\Shared\ApiResponseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/locale', name: 'locale')]
class LocaleController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly LocaleRepository $localeRepository,
        private readonly LocaleService $localeService
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $data = $this->localeRepository->findAll();
        $serializer = LocaleSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Locale $locale): JsonResponse
    {
        if (!$locale) {
            throw new Exception('Locale not found.');
        }

        $serializer = LocaleSerializer::details($locale);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] LocaleDTO $dto
    ): JsonResponse
    {
        $locale = $this->localeService->create($dto);
        $serializer = LocaleSerializer::create($locale);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Locale successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] LocaleDTO $dto, 
        Locale $locale
    ): JsonResponse
    {
        if (!$locale) {
            throw new Exception('Locale not found.');
        }

        $localeService = $this->localeService->update($locale->getId(), $dto);
        $serializer = LocaleSerializer::update($localeService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Locale successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Locale $locale): JsonResponse
    {
        if (!$locale) {
            throw new Exception('Locale not found.');
        }

        $this->localeService->delete($locale);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Locale successfully deleted.']);
    }
}