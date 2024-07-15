<?php

namespace App\Entity\Shop;

use App\Entity\User;
use function Symfony\Component\String\u;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdNameTrait;
use Symfony\Component\Intl\Countries;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\Shop\AddressCustonerRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressCustonerRepository::class)]
class AddressCustoner
{
    use HasIdNameTrait;
    use HasTimestampableTrait;

    #[ORM\Column(length: 255)]
    private ?string $clientName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $moreDetails = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressType = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\Length(min: 4, max: 50)]
    #[Assert\NotBlank]
    private string $street = '';

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Assert\Length(min: 4, max: 50)]
    private ?string $street2 = null;

    #[ORM\Column(type: Types::STRING, length: 5)]
    #[Assert\Length(min: 5, max: 5)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9]{2}\d{3}$/',
        message: 'Invalid postal code.',
        groups: ['order']
    )]
    private string $postalcode = '';

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\Length(min: 4, max: 50)]
    #[Assert\NotBlank]
    private string $city = '';

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\Length(min: 4, max: 100)]
    #[Assert\NotBlank]
    private string $countrycode = '';

    #[ORM\ManyToOne(inversedBy: 'addressCustoners')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function  __toString(){
        return $this->clientName. " - ".
                    $this->street." ".
                    $this->street2." ".
                    $this->postalcode." ".
                    $this->city." ".
                    $this->countrycode." "
                    ;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getMoreDetails(): ?string
    {
        return $this->moreDetails;
    }

    public function setMoreDetails(?string $moreDetails): static
    {
        $this->moreDetails = $moreDetails;

        return $this;
    }

    public function getAddressType(): ?string
    {
        return $this->addressType;
    }

    public function setAddressType(?string $addressType): static
    {
        $this->addressType = $addressType;

        return $this;
    }

    public function stringifyAddress(): string
    {
        $address = '';

        if ($this->street) {
            $address .= $this->street.' ';
        }

        if ($this->street2) {
            $address .= $this->street2.' ';
        }

        if ($this->city) {
            $address .= $this->city.' ';
        }

        if ($this->postalcode) {
            $address .= $this->postalcode.' ';
        }

        if ($this->countrycode) {
            $address .= $this->getCountryCode();
        }

        return $address;
    }

    public function getStreet(): string
    {
        return u($this->street)->upper()->toString();
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getStreet2(): ?string
    {
        return u($this->street2)->upper()->toString();
    }

    public function setStreet2(?string $street2): static
    {
        $this->street2 = $street2;

        return $this;
    }

    public function getPostalcode(): string
    {
        return u($this->postalcode)->upper()->toString();
    }

    public function setPostalcode(string $postalcode): static
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getCity(): string
    {
        return u($this->city)->upper()->toString();
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountryCode(): string
    {
        return u($this->countrycode)->upper()->toString();
    }

    public function setCountryCode(string $countrycode): static
    {
        $this->countrycode = $countrycode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return Countries::getNames()[(int) $this->countrycode] ?? null;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
