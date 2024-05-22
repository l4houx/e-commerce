<?php

namespace App\Entity;

use App\Entity\Traits\HasActiveTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\ColorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ColorRepository::class)]
class Color
{
    use HasIdNameTrait;
    use HasActiveTrait;
    use HasTimestampableTrait;

    #[ORM\Column(type: Types::STRING, length: 128)]
    #[Assert\NotBlank(message: "Please don't leave your name blank!")]
    #[Assert\Length(
        min: 4,
        max: 128,
        minMessage: 'The name is too short ({{ limit }} characters minimum)',
        maxMessage: 'The name is too long ({ limit } characters maximum)'
    )]
    private string $hex = '';

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $display_in_search = null;

    public function getHex(): string
    {
        return $this->hex;
    }

    public function setHex(string $hex): static
    {
        $this->hex = $hex;

        return $this;
    }

    public function getDisplayInSearch(): ?int
    {
        return $this->display_in_search;
    }

    public function setDisplayInSearch(int $display_in_search): static
    {
        $this->display_in_search = $display_in_search;

        return $this;
    }
}
