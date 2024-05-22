<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait HasActiveTrait
{
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    #[Assert\NotNull]
    private int $is_active;

    public function getIsActive(): int
    {
        return $this->is_active;
    }

    public function setIsActive(int $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }
}
