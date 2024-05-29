<?php

namespace App\Entity;

use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasReferenceTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    use HasIdTrait;
    use HasReferenceTrait;
    use HasTimestampableTrait;

    /** -2: failed / -1: cancel / 0: waiting for payment / 1: paid */
    #[ORM\Column(type: Types::INTEGER)]
    private int $status;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank]
    private string $firstname = '';

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank]
    private string $lastname = '';

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\Length(min: 10, max: 20)]
    #[Assert\NotBlank]
    private string $phone = '';

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\NotNull]
    #[Assert\Length(min: 5, max: 180)]
    private string $email = '';

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

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Shipping $countrycode = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Coupon $coupon = null;

    public function __construct()
    {
        $this->status = 0;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    // -2: failed / -1: cancel / 0: waiting for payment / 1: paid
    public function getStatusClass(): string
    {
        switch ($this->status) {
            case -2:
                return 'danger';
                break;
            case -1:
                return 'danger';
                break;
            case 0:
                return 'warning';
                break;
            case 1:
                return 'success';
                break;
            default:
                return 'danger';
        }
    }

    public function stringifyStatus(): string
    {
        switch ($this->status) {
            case -2:
                return 'Failed';
                break;
            case -1:
                return 'Canceled';
                break;
            case 0:
                return 'Awaiting payment';
                break;
            case 1:
                return 'Paid';
                break;
            default:
                return 'Unknown';
        }
    }

    public function getPaymentStatusClass(string $status): string
    {
        if ('new' == $status) {
            return 'info';
        } elseif ('pending' == $status) {
            return 'warning';
        } elseif ('authorized' == $status) {
            return 'success';
        } elseif ('captured' == $status) {
            return 'success';
        } elseif ('canceled' == $status) {
            return 'danger';
        } elseif ('suspended' == $status) {
            return 'danger';
        } elseif ('failed' == $status) {
            return 'danger';
        } elseif ('unknown' == $status) {
            return 'danger';
        }
    }

    public function getFirstname(): ?string
    {
        return u($this->firstname)->upper()->toString();
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return u($this->lastname)->upper()->toString();
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email ?: '';

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

    public function getCountrycode(): ?Shipping
    {
        return $this->countrycode;
    }

    public function setCountrycode(?Shipping $countrycode): static
    {
        $this->countrycode = $countrycode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return Countries::getNames()[$this->countrycode] ?? null;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(?Coupon $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }
}
