<?php

namespace App\DTO;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

final class PictureDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $label;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $slug;

    #[Assert\Type(type: Types::TEXT)]
    public ?string $path;

    #[Assert\Choice(['png', 'jpg', 'jpeg', 'webp', 'svg'])]
    #[Assert\Length(max: 255)]
    public ?string $mimeType; 
    
    #[Assert\Positive(groups: ['create', 'update'])]
    public ?int $bytesSize;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Positive]
    public ?int $width;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Positive]
    public ?int $height;

    #[Assert\Length(max: 255)]
    public ?string $sha256;

    #[Assert\Positive(groups: ['create', 'update'])]
    public ?int $project;
}