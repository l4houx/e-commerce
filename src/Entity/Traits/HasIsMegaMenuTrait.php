<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait HasIsMegaMenuTrait
{
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull]
    private bool $isMegaMenu = false;

    public function isMegaMenu(): bool
    {
        return $this->isMegaMenu;
    }

    public function getIsMegaMenu(): bool
    {
        return $this->isMegaMenu;
    }

    public function setIsMegaMenu(bool $isMegaMenu): static
    {
        $this->isMegaMenu = $isMegaMenu;

        return $this;
    }
}
