<?php

namespace App\Entity\Shop;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdNameTrait;
use App\Repository\Shop\ShippingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ShippingRepository::class)]
#[UniqueEntity('name')]
class Shipping
{
    use HasIdNameTrait;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\GreaterThan(0)]
    private ?float $shippingCost = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'countrycode')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->shippingCost / 100, 2, '.', ',');
    }

    public function getShippingCost(): ?float
    {
        return $this->shippingCost;
    }

    public function setShippingCost(float $shippingCost): static
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCountrycode($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCountrycode() === $this) {
                $order->setCountrycode(null);
            }
        }

        return $this;
    }
}
