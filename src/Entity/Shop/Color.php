<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\Shop\ColorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ColorRepository::class)]
#[UniqueEntity('name')]
class Color
{
    use HasIdNameTrait;
    use HasIsOnlineTrait;
    use HasTimestampableTrait;

    #[ORM\Column(type: Types::STRING, length: 128)]
    #[Assert\NotBlank(message: "Please don't leave your hex blank!")]
    #[Assert\Length(
        min: 4,
        max: 128,
        minMessage: 'The hex is too short ({{ limit }} characters minimum)',
        maxMessage: 'The hex is too long ({ limit } characters maximum)'
    )]
    private string $hex = '';

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $displayInSearch = null;

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
        return $this->displayInSearch;
    }

    public function setDisplayInSearch(int $displayInSearch): static
    {
        $this->displayInSearch = $displayInSearch;

        return $this;
    }
}
