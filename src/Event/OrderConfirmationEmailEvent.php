<?php

namespace App\Event;

use App\Entity\Shop\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderConfirmationEmailEvent extends Event
{
    public function __construct(
        public readonly Order $order
    ) {
    }
}
