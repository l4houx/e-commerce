<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait HasImageUrlTrait
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\Length(max: 255)]
    //#[Assert\NotBlank]
    /*#[Assert\Url(
        message: 'This value is not a valid URL.',
        protocols: ['http', 'https'],
    )]
    */
    private string $imageUrl = '';

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
