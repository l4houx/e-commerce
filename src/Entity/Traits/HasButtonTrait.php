<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait HasButtonTrait
{
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $buttonText = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    #[Assert\Url(
        message: 'This value is not a valid URL.',
        protocols: ['http', 'https'],
    )]
    private string $buttonLink = '';

    public function getButtonText(): string
    {
        return $this->buttonText;
    }

    public function setButtonText(string $buttonText): static
    {
        $this->buttonText = $buttonText;

        return $this;
    }

    public function getButtonLink(): string
    {
        return $this->buttonLink;
    }

    public function setButtonLink(string $buttonLink): static
    {
        $this->buttonLink = $buttonLink;

        return $this;
    }
}
