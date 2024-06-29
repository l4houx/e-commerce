<?php

namespace App\Entity\Company;

use App\Repository\Company\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization extends Company
{
    /**
     * @var Collection<int, Member>
     */
    #[ORM\OneToMany(targetEntity: Member::class, mappedBy: 'organization')]
    private Collection $members;

    public static function getType(): string
    {
        return 'Group';
    }

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }
}
