<?php

namespace App\Entity;

use App\Entity\Traits\HasActiveTrait;
use App\Entity\Traits\HasIdNameTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\SizeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SizeRepository::class)]
class Size
{
    use HasIdNameTrait;
    use HasActiveTrait;
    use HasTimestampableTrait;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $display_in_search = null;

    public function getDisplayInSearch(): ?int
    {
        return $this->display_in_search;
    }

    public function setDisplayInSearch(int $display_in_search): static
    {
        $this->display_in_search = $display_in_search;

        return $this;
    }
}
