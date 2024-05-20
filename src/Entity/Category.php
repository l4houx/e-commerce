<?php

namespace App\Entity;

use App\Entity\Traits\HasIdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use HasIdTrait;

    #[ORM\Column(type: Types::STRING, length: 128, unique: true)]
    #[Assert\NotBlank(message: "Please don't leave your name blank!")]
    #[Assert\Length(
        min: 4,
        max: 128,
        minMessage: 'The name is too short ({{ limit }} characters minimum)',
        maxMessage: 'The name is too long ({ limit } characters maximum)'
    )]
    private string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
