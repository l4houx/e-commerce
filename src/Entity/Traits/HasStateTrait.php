<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HasStateTrait
{
    #[ORM\Column]
    private string $state = 'draft';

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }
}
