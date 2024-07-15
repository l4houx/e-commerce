<?php

namespace App\Entity\Settings;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasMetaTrait;
use App\Entity\Traits\HasViewsTrait;
use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Repository\Settings\PageRepository;
use App\Entity\Traits\HasTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class Page
{
    use HasIdNameSlugTrait;
    use HasContentTrait;
    use HasViewsTrait;
    use HasMetaTrait;
    use HasTimestampableTrait;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull]
    private bool $isHeader = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull]
    private bool $isFooter = false;

    public function __toString(): string
    {
        return (string) $this->getName() ?: '';
    }

    public function isHeader(): bool
    {
        return $this->isHeader;
    }

    public function setIsHeader(bool $isHeader): static
    {
        $this->isHeader = $isHeader;

        return $this;
    }

    public function isFooter(): bool
    {
        return $this->isFooter;
    }

    public function setIsFooter(bool $isFooter): static
    {
        $this->isFooter = $isFooter;

        return $this;
    }
}
