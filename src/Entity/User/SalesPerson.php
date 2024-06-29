<?php

namespace App\Entity\User;

use App\Entity\Company\Client;
use App\Entity\Traits\HasEmployeeTrait;
use App\Entity\User;
use App\Repository\User\SalesPersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AssociationOverride;

#[ORM\Entity(repositoryClass: SalesPersonRepository::class)]
#[ORM\AssociationOverrides([
    new AssociationOverride(
        name: 'member',
        inversedBy: 'salesPersons'
    ),
])]
class SalesPerson extends User
{
    use HasEmployeeTrait;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'salesPerson')]
    private Collection $clients;

    public function __construct()
    {
        parent::__construct();
        $this->clients = new ArrayCollection();
    }

    public function getRole(): string
    {
        return '<span class="badge me-2 bg-info">Sales Person</span>';
    }

    public function getRoleName(): string
    {
        return 'SalesPerson';
    }

    public function getCrossRoleName(): string
    {
        return $this->getFullName();
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }
}
