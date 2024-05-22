<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait HasMetaTrait
{
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $meta_title = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $meta_description = null;

    public function getMetaTitle(): ?string
    {
        return $this->meta_title;
    }

    public function setMetaTitle(?string $meta_title): static
    {
        $this->meta_title = $meta_title;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(?string $meta_description): static
    {
        $this->meta_description = $meta_description;

        return $this;
    }
}
