<?php

namespace App\Event;

use App\Entity\Product;

class ProductCreatedEvent
{
    public function __construct(private readonly Product $product)
    {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
