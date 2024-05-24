<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\AddProductHistoryRepository;

#[ORM\Entity(repositoryClass: AddProductHistoryRepository::class)]
class AddProductHistory
{
    use HasIdTrait;
    use HasTimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'addProductHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $quantity = null;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
