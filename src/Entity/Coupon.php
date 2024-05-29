<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Repository\CouponRepository;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\HasTimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    use HasIdTrait;
    use HasTimestampableTrait;

    #[ORM\Column(type: Types::STRING, length: 10, unique: true)]
    #[Assert\Length(max: 10)]
    #[Assert\NotBlank]
    private ?string $code = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $content = '';

    #[ORM\Column(type: Types::INTEGER)]
    private int $discount;

    #[ORM\Column(type: Types::INTEGER)]
    private int $maxUsage;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validity;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Assert\NotNull]
    private bool $isValid = true;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'coupon')]
    private Collection $orders;

    #[ORM\ManyToOne(inversedBy: 'coupons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CouponType $couponType = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getMaxUsage(): int
    {
        return $this->maxUsage;
    }

    public function setMaxUsage(int $maxUsage): static
    {
        $this->maxUsage = $maxUsage;

        return $this;
    }

    public function getValidity(): \DateTimeImmutable
    {
        return $this->validity;
    }

    public function setValidity(\DateTimeImmutable $validity): static
    {
        $this->validity = $validity;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setValid(bool $isValid): static
    {
        $this->isValid = $isValid;

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
            $order->setCoupon($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCoupon() === $this) {
                $order->setCoupon(null);
            }
        }

        return $this;
    }

    public function getCouponType(): ?CouponType
    {
        return $this->couponType;
    }

    public function setCouponType(?CouponType $couponType): static
    {
        $this->couponType = $couponType;

        return $this;
    }
}
