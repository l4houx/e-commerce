<?php

namespace App\Entity\Shop;

use App\Entity\Shop\Product;
use App\Entity\Traits\HasIdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: '`order_line`')]
class Line
{
    use HasIdTrait;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\Positive()]
    #[Assert\LessThan(1001)]
    private ?float $price = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

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
        return $this->quantity * $this->price;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        $this->price = $product->getPrice();

        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;

        return $this;
    }
}
