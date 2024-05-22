<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasMetaTrait;
use App\Repository\BrandRepository;
use App\Entity\Traits\HasActiveTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    use HasIdNameTrait;
    use HasMetaTrait;
    use HasActiveTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $order = null;

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): static
    {
        $this->order = $order;

        return $this;
    }
}
