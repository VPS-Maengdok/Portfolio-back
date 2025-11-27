<?php

namespace App\Serializer;

use App\Entity\Picture;

final class PictureSerializer
{
    public static function list(array $pictures): array
    {
        return array_map(function ($picture) {
            return PictureSerializer::details($picture);
        }, $pictures);
    }

    public static function details(Picture $picture): array
    {
        return [
            'id' => $picture->getId(),
            'label' => $picture->getLabel(),
            'slug' => $picture->getSlug(),
            'path' => $picture->getPath(),
            'mimeType' => $picture->getMimeType(),
            'bytesSize' => $picture->getBytesSize(),
            'width' => $picture->getWidth(),
            'height' => $picture->getHeight(),
            'sha256' => $picture->getSha256(),
            'createdAt' => $picture->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    public function create(Picture $picture): array
    {
        return $this->details($picture);
    }

    public function update(Picture $picture): array
    {
        return $this->details($picture);
    }
}