<?php

declare(strict_types=1);

namespace App\Controller\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class OrderDetailField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): OrderDetailField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/order_detail.html.twig')
        ;
    }
}
