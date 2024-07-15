<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait HasDescriptionTrait
{
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 30)]
    private string $description = '';

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getExcerpts(): string
    {
        if (null === $this->description) {
            return '';
        }

        $parts = preg_split("/(\r\n|\r|\n){2}/", $this->description);

        return false === $parts ? '' : strip_tags($parts[0]);
    }
}
