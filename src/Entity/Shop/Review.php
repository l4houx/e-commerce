<?php

namespace App\Entity\Shop;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasRatingTrait;
use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Repository\Shop\ReviewRepository;
use App\Entity\Traits\HasTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class Review
{
    use HasIdNameSlugTrait;
    use HasRatingTrait;
    use HasContentTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    // #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Assert\NotNull]
    private bool $isVisible = true;

    public function __construct()
    {
        $this->isVisible = true;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function getIsVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;

        return $this;
    }
}
