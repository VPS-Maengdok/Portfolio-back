<?php

namespace App\Controller;

use App\DTO\TagDTO;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Serializer\TagSerializer;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use App\Service\TagService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag', name: 'tag')]
class TagController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly TagSerializer $tagSerializer,
        private readonly TagService $tagService,
        private readonly TagRepository $tagRepository
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocaleFromRequest($request);
        $data = $this->tagRepository->findAllWithLocale($lang->getId());
        $serializer = $this->tagSerializer->list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Tag $tag, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$tag) {
            throw new Exception('Tag not found.');
        }

        $lang = $localeRequestService->getLocaleFromRequest($request);
        $data = $this->tagRepository->findOneWithLocale($tag->getId(), $lang->getId());
        $serializer = $this->tagSerializer->details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] TagDTO $dto
    ): JsonResponse
    {
        $tag = $this->tagService->create($dto);
        $serializer = $this->tagSerializer->create($tag);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Tag successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] TagDTO $dto, 
        Tag $tag
    ): JsonResponse
    {
        if (!$tag) {
            throw new Exception('Tag not found.');
        }

        $tagService = $this->tagService->update($tag->getId(), $dto);
        $serializer = $this->tagSerializer->update($tagService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Tag successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Tag $tag): JsonResponse
    {
        if (!$tag) {
            throw new Exception('Tag not found.');
        }

        $this->tagService->delete($tag);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Tag successfully deleted.']);
    }
}