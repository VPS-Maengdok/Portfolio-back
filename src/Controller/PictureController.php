<?php

namespace App\Controller;

use App\DTO\PictureDTO;
use App\Entity\Picture;
use App\Repository\PictureRepository;
use App\Serializer\PictureSerializer;
use App\Service\PictureService;
use App\Service\Shared\ApiResponseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/picture', name: 'picture')]
class PictureController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseService $apiResponse,
        private readonly PictureRepository $pictureRepository,
        private readonly PictureService $pictureService,
        private readonly PictureSerializer $pictureSerializer
    ) {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $data = $this->pictureRepository->findAll();
        $serializer = PictureSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Picture $picture): JsonResponse
    {
        if (!$picture) {
            throw new Exception('Picture not found.');
        }

        $data = $this->pictureRepository->findOneById($picture->getId());
        $serializer = PictureSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/', name: '_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            validationGroups: ['create'], 
        )] PictureDTO $dto,
        #[MapUploadedFile] UploadedFile $file
    ): JsonResponse
    {
        if (!$file) {
            return new JsonResponse(['error' => 'No file provided'], 400);
        }
    
        if (!$file->isValid()) {
            return new JsonResponse(['error' => 'Invalid upload'], 400);
        }

        $picture = $this->pictureService->create($dto, $file);
        $serializer = $this->pictureSerializer->create($picture);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Picture successfully created.'], $serializer);
    }

    #[Route('/{id}', name: '_update', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function update(
        #[MapRequestPayload(
            validationGroups: ['update'], 
        )] PictureDTO $dto, 
        #[MapUploadedFile] ?UploadedFile $file,
        Picture $picture
    ): JsonResponse
    {
        if (!$picture) {
            throw new Exception('Picture not found.');
        }

        if($file) {        
            if (!$file->isValid()) {
                return new JsonResponse(['error' => 'Invalid upload'], 400);
            }

            $pictureService = $this->pictureService->update($picture, $dto, $file);
        } else {
            $pictureService = $this->pictureService->update($picture, $dto);
        }

        $serializer = $this->pictureSerializer->update($pictureService);

        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Picture successfully updated.'], $serializer);
    }

    #[Route('/{id}', name: '_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Picture $picture): JsonResponse
    {
        if (!$picture) {
            throw new Exception('Picture not found.');
        }

        $this->pictureService->delete($picture);
        
        return $this->apiResponse->getApiResponse(200, ['result' => 'Success', 'msg' => 'Picture successfully deleted.']);
    }
}