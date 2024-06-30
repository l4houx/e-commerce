<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait HasIsPayOnDeliveryTrait
{
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $isPayOnDelivery = false;

    public function isPayOnDelivery(): bool
    {
        return $this->isPayOnDelivery;
    }

    public function getIsPayOnDelivery(): bool
    {
        return $this->isPayOnDelivery;
    }

    public function setIsPayOnDelivery(bool $isPayOnDelivery): static
    {
        $this->isPayOnDelivery = $isPayOnDelivery;

        return $this;
    }
}
