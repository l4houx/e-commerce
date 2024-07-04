<?php

namespace App\Entity\Shop;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Repository\Shop\OrderDetailRepository;

#[ORM\Entity(repositoryClass: OrderDetailRepository::class)]
class OrderDetail
{
    use HasIdTrait;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    //#[ORM\Column(type: Types::FLOAT)]
    //#[Assert\Positive()]
    //#[Assert\LessThan(1001)]
    //private ?float $price = null;

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        //$this->price = $product->getPrice();

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    // ADD 2 JUIL
    public function decreaseQuantity(): static
    {
        --$this->quantity;

        return $this;
    }

    public function increaseQuantity(): static
    {
        ++$this->quantity;

        return $this;
    }

    public function getTotal(): int
    {
        //return $this->quantity * $this->price;
        return $this->quantity;
    }

    /*public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }*/
}
