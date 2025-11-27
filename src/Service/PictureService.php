<?php

namespace App\Service;

use App\DTO\PictureDTO;
use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly EntityManagerInterface     $em,
        private readonly Filesystem                 $filesystem,
        #[Autowire('%app.upload_dir%')] 
        private readonly string                     $uploadDir
    ) {}

    public function create(PictureDTO $dto, UploadedFile $file): Picture
    {
        $hydratedPicture = $this->hydratePicture(new Picture(), $dto);

        $this->relationService->setRelations($hydratedPicture, $dto);
        $this->uploadFile($file, $dto, $hydratedPicture);

        $this->em->persist($hydratedPicture);
        $this->em->flush();

        return $hydratedPicture;
    }

    public function update(Picture $picture, PictureDTO $dto, ?UploadedFile $file = null): Picture
    {
        $hydratedPicture = $this->hydratePicture($picture, $dto);
        $this->relationService->setRelations($hydratedPicture, $dto);

        if ($file) {
            $this->deleteFile($picture);
            $this->uploadFile($file, $dto, $hydratedPicture);
        }


        $this->em->flush();

        return $hydratedPicture;
    }

    public function delete(Picture $picture): void
    {
        $this->deleteFile($picture);
        $this->em->remove($picture);
        $this->em->flush();
    }

    private function uploadFile(UploadedFile $file, PictureDTO $dto, Picture $picture): void
    {
        $path = $this->uploadDir . "/projects/{$dto->project}/";
        $this->verifyDirectory($path);

        $sha256 = hash_file('sha256', $file->getRealPath());
        $size = $file->getSize();
        $randomBytes = bin2hex(random_bytes(8));
        $extension = $file->guessExtension();
        $fileName = sprintf('%s_%s.%s', $dto->slug, $randomBytes, $extension);
        $relativePath = "projects/{$dto->project}/{$fileName}";
        
        $file->move($path, $fileName);

        $picture
            ->setLabel($fileName)
            ->setPath($relativePath)
            ->setBytesSize($size)
            ->setMimeType($extension)
            ->setSha256($sha256);
    }

    private function verifyDirectory(string $path): void {
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path);
        }
    }

    private function deleteFile(Picture $picture): void
    {
        $filePath = $this->uploadDir . '/' . $picture->getPath();
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }

    }

    private function hydratePicture(Picture $picture, PictureDTO $dto): Picture
    {
        return $picture
            ->setSlug($dto->slug)
            ->setWidth($dto->width)
            ->setHeight($dto->height);
    }
}