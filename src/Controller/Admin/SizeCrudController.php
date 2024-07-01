<?php

namespace App\Controller\Admin;

use App\Entity\Shop\Size;
use App\Entity\Traits\HasRoles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class SizeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Size::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();
        yield IntegerField::new('displayInSearch', t('display In Search'));

        yield TextField::new('name', t('Name'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 3, max: 128),
            ])
        ;

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isOnline', t('Published'));

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
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
