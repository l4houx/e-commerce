<?php

namespace App\Controller\Admin;

use App\Entity\Shop\AddressCustoner;
use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotBlank;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AddressCustonerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AddressCustoner::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield AssociationField::new('user', t('User'));
        yield TextField::new('name', t('Address Name'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 128),
            ])
        ;
        yield TextField::new('clientName', t('Client Name'));
        yield ChoiceField::new('addressType', t('Address Type'))->setChoices([
            'Billing' => 'Billing',
            'Shipping' => 'Shipping'
        ]);
        yield TextEditorField::new('moreDetails', t('More Details'));

        yield FormField::addTab(t('Address'))->hideOnIndex();
        yield TextField::new('street', t('Address'))->hideOnIndex();
        yield TextField::new('street2', t('Additional address'))->hideOnIndex();
        yield TextField::new('postalcode', t('Postal code'))->hideOnIndex();
        yield TextField::new('city', t('City'))->hideOnIndex();
        yield CountryField::new('countrycode', t('Country'))->hideOnIndex();

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions{
        return $actions 
        ->add(Crud::PAGE_EDIT, Action::INDEX)
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::DETAIL)
        ;
    }
}
