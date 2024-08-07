<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait HasShopDetailsTrait
{
    use HasIdNameSlugTrait;
    use HasMetaTrait;
    use HasIsOnlineTrait;
    use HasViewsTrait;
    use HasTagTrait;
    use HasSocialNetworksTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;


    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty: 'imageName')]
    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'],
        mimeTypesMessage: 'The file should be an image'
    )]
    #[Assert\NotNull(groups: ['create'])]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 100)]
    private string $content = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 100)]
    private ?string $moreContent = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 100)]
    private ?string $additionalInformation = null;

    #[ORM\Column(type: Types::STRING, length: 500, unique: true)]
    private string $ref = '';

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\Positive()]
    #[Assert\LessThan(1001)]
    private ?float $price = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\Positive()]
    #[Assert\LessThan(1001)]
    private ?float $salePrice = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\PositiveOrZero(message: 'Stock cannot be negative')]
    private ?int $stock = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $isFeaturedProduct = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $isBestSelling = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $isNewArrival = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $isSpecialOffer = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $isAvailable = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    #[Assert\NotNull(groups: ['create', 'update'])]
    private bool $enablereviews = true;

    public function setImageFile(File|UploadedFile|null $imageFile): static
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImagePath(): string
    {
        return 'uploads/product/'.$this->imageName;
    }

    public function getImagePlaceholder(string $size = 'default'): string
    {
        if ('small' == $size) {
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAABGCAMAAABG8BK2AAAA/1BMVEUAAAD2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhH2dhFzESFhAAAAVHRSTlMAAQIDBQYICQsNDhAREhQVGBobISQmLC0wMTQ1ODlAQkNLT1xdY2ZpbXR1d3mDhoiLjpGSlZeYnaCjpaaqtLq8yMrMzs/R1d7k5ujp6+3z9ff5+/1JGcHEAAABMUlEQVRYw+3W2U4CQRCF4dMzAqO47zvuu6iAorjgDjpuQL3/s3jjBTCkulLpRBP7f4AvmaqeTgM+n8/3Gw2un5R/Km6NKRGzRx2VAhWTp66uNMoUJcopmLMk86BgXpMMKabTQ6EoaM9oma7i3YwLhuhjwglDXwNOGDp3w1Dohsm6YYb/PVOci0Rnn2Oa2ykV0clU+9VIG1MyANC3tHNaZjqa4ZlLAyB9aJ91xDF3IYCFhl2JDcO8ZACsSBY/yXxUYwQ9L/dkq9yIpwFkPwXKPrepNQCpmkCpcAs/AGBuBMp9yDAXAFAQKDFzs9NtAGBToDRHmVNcTwNYlCxpnvsZhgCMtwTKBixFbwLl2KaEjwLl2noN5QVKxf46sA/mfdmKALlnjmg9FWZ1zzifz+f7c30DdESL++xXlRkAAAAASUVORK5CYII=';
        } else {
            return 'data:image/png;base64,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';
        }
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getMoreContent(): ?string
    {
        return $this->moreContent;
    }

    public function setMoreContent(?string $moreContent): static
    {
        $this->moreContent = $moreContent;

        return $this;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }

    public function setAdditionalInformation(?string $additionalInformation): static
    {
        $this->additionalInformation = $additionalInformation;

        return $this;
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function setRef(string $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSalePrice(): ?float
    {
        return $this->salePrice;
    }

    public function setSalePrice(float $salePrice): static
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isFeaturedProduct(): bool
    {
        return $this->isFeaturedProduct;
    }

    public function getIsFeaturedProduct(): bool
    {
        return $this->isFeaturedProduct;
    }

    public function setIsFeaturedProduct(bool $isFeaturedProduct): static
    {
        $this->isFeaturedProduct = $isFeaturedProduct;

        return $this;
    }

    public function isBestSelling(): bool
    {
        return $this->isBestSelling;
    }

    public function getIsBestSelling(): bool
    {
        return $this->isBestSelling;
    }

    public function setIsBestSelling(bool $isBestSelling): static
    {
        $this->isBestSelling = $isBestSelling;

        return $this;
    }

    public function isNewArrival(): bool
    {
        return $this->isNewArrival;
    }

    public function getIsNewArrival(): bool
    {
        return $this->isNewArrival;
    }

    public function setIsNewArrival(bool $isNewArrival): static
    {
        $this->isNewArrival = $isNewArrival;

        return $this;
    }

    public function isSpecialOffer(): bool
    {
        return $this->isSpecialOffer;
    }

    public function getIsSpecialOffer(): bool
    {
        return $this->isSpecialOffer;
    }

    public function setIsSpecialOffer(bool $isSpecialOffer): static
    {
        $this->isSpecialOffer = $isSpecialOffer;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function getIsAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function isEnablereviews(): bool
    {
        return $this->enablereviews;
    }

    public function getIsEnablereviews(): bool
    {
        return $this->enablereviews;
    }

    public function setIsEnablereviews(bool $enablereviews): static
    {
        $this->enablereviews = $enablereviews;

        return $this;
    }
}
