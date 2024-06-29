<?php

namespace App\Controller\Admin;

use App\Entity\Shop\Size;
use App\Entity\Traits\HasRoles;
use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SizeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Size::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Size'))
            ->setEntityLabelInPlural(t('Sizes'))
            ->setDefaultSort(['name' => 'asc'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(TextFilter::new('name', t('Name')));
    }
}
