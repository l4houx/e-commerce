<?php

declare(strict_types=1);

namespace App\Controller\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class OrderIsCompletedField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): OrderIsCompletedField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath("admin/field/order_is_completed.html.twig")
        ;
    }
}
