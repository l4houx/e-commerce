<?php

namespace App\Entity;

use App\Entity\Traits\HasActiveTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasMetaTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\BrandRepository;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
#[UniqueEntity('name')]
//#[Vich\Uploadable]
class Brand
{
    use HasIdNameTrait;
    use HasMetaTrait;
    use HasActiveTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    /*
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
    */
}
