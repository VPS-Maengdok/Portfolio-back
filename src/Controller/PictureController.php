<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use App\Serializer\PictureSerializer;
use App\Service\Shared\ApiResponseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/picture', name: 'picture')]
class PictureController extends AbstractController
{
    public function __construct(private readonly ApiResponseService $apiResponse)
    {}

    #[Route('/', name: '_list', methods: ['GET'])]
    public function list(PictureRepository $pictureRepository): JsonResponse
    {
        $data = $pictureRepository->findAll();
        $serializer = PictureSerializer::list($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }

    #[Route('/{id}', name: '_details', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function details(Picture $picture, PictureRepository $pictureRepository): JsonResponse
    {
        if (!$picture) {
            throw new Exception('Picture not found.');
        }

        $data = $pictureRepository->findOneById($picture->getId());
        $serializer = PictureSerializer::details($data);

        return $this->apiResponse->getApiResponse(code: 200, data: $serializer);
    }
}