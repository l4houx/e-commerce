<?php

namespace App\DataTransferObject;

use App\Entity\Shop\OrderDetail;

class SavFormDTO
{
    public OrderDetail $orderDetail;

    public string $content = '';

    public ?string $comment = null;

    /**
     * @var array<int, string>
     */
    public array $attachments = [];
}
