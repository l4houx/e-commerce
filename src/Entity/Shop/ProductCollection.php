<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasButtonTrait;
use App\Entity\Traits\HasDescriptionTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasImageUrlTrait;
use App\Entity\Traits\HasIsMegaMenuTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\Shop\ProductCollectionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductCollectionRepository::class)]
#[UniqueEntity('name')]
class ProductCollection
{
    use HasIdNameTrait;
    use HasDescriptionTrait;
    use HasButtonTrait;
    use HasImageUrlTrait;
    use HasIsMegaMenuTrait;
    use HasTimestampableTrait;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
