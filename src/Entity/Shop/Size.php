<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\Shop\SizeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SizeRepository::class)]
#[UniqueEntity('name')]
class Size
{
    use HasIdNameTrait;
    use HasIsOnlineTrait;
    use HasTimestampableTrait;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $displayInSearch = null;

    public function getDisplayInSearch(): ?int
    {
        return $this->displayInSearch;
    }

    public function setDisplayInSearch(int $displayInSearch): static
    {
        $this->displayInSearch = $displayInSearch;

        return $this;
    }
}
