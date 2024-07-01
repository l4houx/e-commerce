<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\OrderDetailField;
use App\Controller\Admin\Field\OrderStatusField;
use App\Controller\Admin\Traits\DetailOnlyTrait;
use App\Entity\Shop\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

use function Symfony\Component\Translation\t;

class OrderCrudController extends AbstractCrudController
{
    use DetailOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', t('Order No'))->onlyOnIndex();
        yield TextField::new('ref', t('Reference product'));
        yield DateTimeField::new('createdAt', t('Order date'));
        yield IntegerField::new('totalPrice', t('Total price'));
        yield OrderStatusField::new('status', t('Status'));
        yield OrderDetailField::new('orderDetails', t('Command details'))->onlyOnDetail();
        yield AssociationField::new('coupon', t('Coupon'))->onlyOnDetail();

        yield FormField::addPanel(t('Client'))->onlyOnDetail();
        yield TextField::new('firstname', t('Firstname'))->onlyOnDetail();
        yield TextField::new('lastname', t('Lastname'))->onlyOnDetail();
        yield EmailField::new('email', t('Email'))->onlyOnDetail();
        yield TextField::new('phone', t('Phone Number'))->onlyOnDetail();

        yield FormField::addPanel(t('Address'))->onlyOnDetail();
        yield TextField::new('street', t('Address'))->onlyOnDetail();
        yield TextField::new('street2', t('Additional address'))->onlyOnDetail();
        yield TextField::new('postalcode', t('Postal code'))->onlyOnDetail();
        yield TextField::new('city', t('City'))->onlyOnDetail();
        yield AssociationField::new('countrycode', t('Country'))->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Order account'))
            ->setEntityLabelInPlural(t('Orders accounts'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(DateTimeFilter::new('createdAt', t('Creation date')));
    }
}
