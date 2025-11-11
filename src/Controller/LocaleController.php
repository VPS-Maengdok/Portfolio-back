<?php

namespace App\Controller;

use App\Entity\Locale;
use App\Repository\LocaleRepository;
use App\Serializer\LocaleSerializer;
use App\Service\Shared\ApiResponseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/locale', name: 'locale')]
class LocaleController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(LocaleRepository $localeRepository): JsonResponse
    {
        $data = $localeRepository->findAll();
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
}