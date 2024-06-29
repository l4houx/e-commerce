<?php

namespace App\Event;

use App\Entity\Shop\Product;

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
