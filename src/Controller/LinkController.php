<?php

namespace App\Controller;

use App\DTO\LinkDTO;
use App\Entity\Link;
use App\Repository\LinkRepository;
use App\Serializer\LinkSerializer;
use App\Service\LinkService;
use App\Service\Shared\ApiResponseService;
use App\Service\Shared\LocaleRequestService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/link', name: 'link')]
class LinkController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly LinkRepository $linkRepository,
        private readonly LinkService $linkService
    ){}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(Request $request, LocaleRequestService $localeRepository): JsonResponse
    {
        $lang = $localeRepository->getLocale($request);
        $data = $this->linkRepository->findAllWithLocale($lang->getId());
        $serializer = LinkSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Request $request, Link $link, LocaleRequestService $localeRequestService): JsonResponse
    {
        if (!$link) {
            throw new Exception('Link not found.');
        }

        $lang = $localeRequestService->getLocale($request);
        $data = $this->linkRepository->findOneWithLocale($link->getId(), $lang->getId());
        $serializer = LinkSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
            acceptFormat: 'json'
        )] LinkDTO $dto
    ): JsonResponse
    {
        $link = $this->linkService->create($dto);
        $serializer = LinkSerializer::create($link);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Link successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
            acceptFormat: 'json'
        )] LinkDTO $dto, 
        Link $link
    ): JsonResponse
    {
        if (!$link) {
            throw new Exception('Link not found.');
        }

        $linkService = $this->linkService->update($link->getId(), $dto);
        $serializer = LinkSerializer::update($linkService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Link successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Link $link): JsonResponse
    {
        if (!$link) {
            throw new Exception('Link not found.');
        }

        $this->linkService->delete($link);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Link successfully deleted.']);
    }
}