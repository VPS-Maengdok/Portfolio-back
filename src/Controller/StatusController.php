<?php

namespace App\Controller;

use App\Entity\Status;
use App\Repository\StatusRepository;
use App\Serializer\StatusSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/status', name: 'status')]
class StatusController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly StatusSerializer $statusSerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, StatusRepository $statusRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $statusRepository->findAllWithLocale($lang->getId());
        $serializer = $this->statusSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Status $status, LocaleRequestService $localeRequestService, StatusRepository $statusRepository): JsonResponse
    {
        if (!$status) {
            throw new Exception('Status not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $statusRepository->findOneWithLocale($status->getId(), $lang->getId());
        $serializer = $this->statusSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}