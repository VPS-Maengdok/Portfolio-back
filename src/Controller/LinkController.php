<?php

namespace App\Controller;

use App\Entity\Link;
use App\Repository\LinkRepository;
use App\Serializer\LinkSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/link', name: 'link')]
class LinkController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, LinkRepository $linkRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $linkRepository->findAllWithLocale($lang->getId());
        $serializer = LinkSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Link $link, LocaleRequestService $localeRequestService, LinkRepository $linkRepository): JsonResponse
    {
        if (!$link) {
            throw new Exception('Link not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $linkRepository->findOneWithLocale($link->getId(), $lang->getId());
        $serializer = LinkSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}