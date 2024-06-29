<?php

namespace App\Event;

use App\Entity\Shop\Product;

class PreProductCreatedEvent
{
    public function __construct(private readonly Product $product)
    {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
