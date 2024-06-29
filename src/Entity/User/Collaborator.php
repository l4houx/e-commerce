<?php

namespace App\Entity\User;

use App\Entity\Traits\HasEmployeeTrait;
use App\Entity\User;
use App\Repository\User\CollaboratorRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AssociationOverride;

#[ORM\Entity(repositoryClass: CollaboratorRepository::class)]
#[ORM\AssociationOverrides([
    new AssociationOverride(
        name: 'member',
        inversedBy: 'collaborators'
    ),
])]
class Collaborator extends User
{
    use HasEmployeeTrait;

    public function getRole(): string
    {
        return '<span class="badge me-2 bg-dark">Collaborator</span>';
    }

    public function getRoleName(): string
    {
        return 'Collaborator';
    }

    public function getCrossRoleName(): string
    {
        return $this->getFullName();
    }
}
