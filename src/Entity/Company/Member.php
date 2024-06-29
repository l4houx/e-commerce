<?php

namespace App\Entity\Company;

use App\Entity\Address;
use App\Entity\Data\Account;
use App\Entity\User\Collaborator;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use App\Repository\Company\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\Table(name: '`member`')]
class Member extends Company
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'member')]
    private Collection $clients;

    #[ORM\ManyToOne(inversedBy: 'members')]
    private ?Organization $organization = null;

    /**
     * @var Collection<int, Manager>
     */
    #[ORM\ManyToMany(targetEntity: Manager::class, mappedBy: 'members')]
    private Collection $managers;

    /**
     * @var Collection<int, Collaborator>
     */
    #[ORM\OneToMany(targetEntity: Collaborator::class, mappedBy: 'member')]
    private Collection $collaborators;

    /**
     * @var Collection<int, SalesPerson>
     */
    #[ORM\OneToMany(targetEntity: SalesPerson::class, mappedBy: 'member')]
    private Collection $salesPersons;

    #[ORM\OneToOne(inversedBy: 'member', cascade: ['persist'], fetch: 'EAGER')]
    private ?Account $account = null;

    public static function getType(): string
    {
        return 'Member';
    }

    public function __construct()
    {
        $this->address = new Address();
        $this->clients = new ArrayCollection();
        $this->managers = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
        $this->salesPersons = new ArrayCollection();
        $this->account = new Account();
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Collection<int, Manager>
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    /**
     * @return Collection<int, Collaborator>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    /**
     * @return Collection<int, SalesPerson>
     */
    public function getSalesPersons(): Collection
    {
        return $this->salesPersons;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
}
