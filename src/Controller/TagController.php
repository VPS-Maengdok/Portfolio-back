<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Serializer\TagSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag', name: 'tag')]
class TagController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository, TagRepository $tagRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $tagRepository->findAllWithLocale($lang->getId());
        $serializer = TagSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Tag $tag, LocaleRequestService $localeRequestService, TagRepository $tagRepository): JsonResponse
    {
        if (!$tag) {
            throw new Exception('Tag not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $tagRepository->findOneWithLocale($tag->getId(), $lang->getId());
        $serializer = TagSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}