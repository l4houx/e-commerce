<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\DetailEditNewOnlyTrait;
use App\Entity\Shop\Coupon;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class CouponCrudController extends AbstractCrudController
{
    use DetailEditNewOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Coupon::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('code', t('Code'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(max: 10),
            ])
        ;
        yield TextareaField::new('content', t('Content'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
            ])
            ->renderAsHtml()->hideOnIndex()
        ;
        yield IntegerField::new('discount', t('Discount'));
        yield IntegerField::new('maxUsage', t('Max usage'));

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isValid', t('Valid'));

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('validity', t('Validity date'));
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Coupon'))
            ->setEntityLabelInPlural(t('Coupons'))
            ->setDefaultSort(['createdAt' => 'DESC', 'validity' => 'ASC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
            ->add(DateTimeFilter::new('validity', t('Validity date')))
        ;
    }
}
