<?php

namespace App\Entity\Shop;

use App\Entity\Settings\HomepageHeroSetting;
use App\Entity\SuperAdministrator;
use App\Entity\Traits\HasShopDetailsTrait;
use App\Entity\User;
use App\Repository\Shop\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
#[Vich\Uploadable]
class Product
{
    use HasShopDetailsTrait;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\ManyToMany(targetEntity: SubCategory::class, inversedBy: 'products')]
    private Collection $subCategories;

    #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;

    /**
     * @var Collection<int, ProductImage>
     */
    #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'product', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    /**
     * @var Collection<int, AddProductHistory>
     */
    #[ORM\OneToMany(targetEntity: AddProductHistory::class, mappedBy: 'product')]
    private Collection $addProductHistories;

    #[ORM\ManyToOne(inversedBy: 'products', cascade: ['persist'])]
    private ?HomepageHeroSetting $isonhomepageslider = null;

    /**
     * @var collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'favorites', cascade: ['persist', 'remove'])]
    #[ORM\JoinTable(name: 'favorites')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private Collection $addedtofavoritesby;

    /**
     * @var Collection<int, FeatureValue>
     */
    #[ORM\OneToMany(targetEntity: FeatureValue::class, mappedBy: 'product', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(1)]
    #[Assert\Valid]
    private Collection $features;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'product', orphanRemoval: true, cascade: ['remove'])]
    private Collection $reviews;

    /**
     * @var Collection<int, OrderDetail>
     */
    #[ORM\OneToMany(targetEntity: OrderDetail::class, mappedBy: 'product')]
    private Collection $orderDetails;

    public function stringifyStatus(): string
    {
        if (!$this->isOnline) {
            return 'Offline';
        } elseif (!$this->isOnline) {
            return 'Draft';
        } elseif (!$this->isOnline) {
            return 'Schedule';
        } else {
            return 'Online';
        }
    }

    public function stringifyStatusClass(): string
    {
        if (!$this->isOnline) {
            return 'danger';
        } elseif (!$this->isOnline) {
            return 'warning';
        } elseif (!$this->isOnline) {
            return 'info';
        } else {
            return 'success';
        }
    }

    public function __toString(): string
    {
        return sprintf('#%d %s', $this->getId(), $this->getName());
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->subCategories = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->addProductHistories = new ArrayCollection();
        $this->addedtofavoritesby = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
    }

    public function hasContactAndSocialMedia(): bool
    {
        return $this->externallink
            || $this->phone || $this->email
            || $this->youtubeurl || $this->twitterurl
            || $this->instagramurl || $this->facebookurl
            || $this->googleplusurl || $this->linkedinurl
        ;
    }

    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): static
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): static
    {
        $this->subCategories->removeElement($subCategory);

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AddProductHistory>
     */
    public function getAddProductHistories(): Collection
    {
        return $this->addProductHistories;
    }

    public function addAddProductHistory(AddProductHistory $addProductHistory): static
    {
        if (!$this->addProductHistories->contains($addProductHistory)) {
            $this->addProductHistories->add($addProductHistory);
            $addProductHistory->setProduct($this);
        }

        return $this;
    }

    public function removeAddProductHistory(AddProductHistory $addProductHistory): static
    {
        if ($this->addProductHistories->removeElement($addProductHistory)) {
            // set the owning side to null (unless already changed)
            if ($addProductHistory->getProduct() === $this) {
                $addProductHistory->setProduct(null);
            }
        }

        return $this;
    }

    public function getIsonhomepageslider(): ?HomepageHeroSetting
    {
        return $this->isonhomepageslider;
    }

    public function setIsonhomepageslider(?HomepageHeroSetting $isonhomepageslider): static
    {
        $this->isonhomepageslider = $isonhomepageslider;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAddedtofavoritesby(): Collection
    {
        return $this->addedtofavoritesby;
    }

    public function addAddedtofavoritesby(SuperAdministrator|User $addedtofavoritesby): static
    {
        if (!$this->addedtofavoritesby->contains($addedtofavoritesby)) {
            $this->addedtofavoritesby->add($addedtofavoritesby);
        }

        return $this;
    }

    public function removeAddedtofavoritesby(SuperAdministrator|User $addedtofavoritesby): static
    {
        $this->addedtofavoritesby->removeElement($addedtofavoritesby);

        return $this;
    }

    public function isAddedToFavoritesBy(SuperAdministrator|User $user): bool
    {
        return $this->addedtofavoritesby->contains($user);
    }

    /**
     * @return Collection<int, FeatureValue>
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(FeatureValue $feature): static
    {
        if (!$this->features->contains($feature)) {
            $this->features->add($feature);
            $feature->setProduct($this);
        }

        return $this;
    }

    public function removeFeature(FeatureValue $feature): static
    {
        if ($this->features->removeElement($feature)) {
            // set the owning side to null (unless already changed)
            if ($feature->getProduct() === $this) {
                $feature->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    public function isRatedBy(User|SuperAdministrator $user)
    {
        foreach ($this->reviews as $review) {
            if ($review->getAuthor() === $user) {
                return $review;
            }
        }

        return false;
    }

    public function getRatingsPercentageForRating($rating): int|float
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        return round(($this->getRatingsCountForRating($rating) / $this->countVisibleReviews()) * 100, 1);
    }

    public function getRatingsCountForRating($rating): int
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        $ratingCount = 0;

        foreach ($this->reviews as $review) {
            if ($review->getIsVisible() && $review->getRating() === $rating) {
                ++$ratingCount;
            }
        }

        return $ratingCount;
    }

    public function getRatingAvg()
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        $ratingAvg = 0;

        foreach ($this->reviews as $review) {
            if ($review->getIsVisible()) {
                $ratingAvg += $review->getRating();
            }
        }

        return round($ratingAvg / $this->countVisibleReviews(), 1);
    }

    public function getRatingPercentage()
    {
        if (!$this->countVisibleReviews()) {
            return 0;
        }

        $ratingPercentage = 0;

        foreach ($this->reviews as $review) {
            if ($review->getIsVisible()) {
                $ratingPercentage += $review->getRatingPercentage();
            }
        }

        return round($ratingPercentage / $this->countVisibleReviews(), 1);
    }

    public function countVisibleReviews(): int
    {
        $count = 0;

        foreach ($this->reviews as $review) {
            if ($review->getIsVisible()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setProduct($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduct() === $this) {
                $orderDetail->setProduct(null);
            }
        }

        return $this;
    }
}
