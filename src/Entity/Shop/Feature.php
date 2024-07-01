<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasIdNameTrait;
use App\Repository\Shop\FeatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
#[UniqueEntity('name')]
class Feature
{
    use HasIdNameTrait;
}
