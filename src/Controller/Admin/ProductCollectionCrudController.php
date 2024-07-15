<?php

namespace App\Controller\Admin;

use App\Entity\Shop\ProductCollection;
use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Validator\Constraints\Url;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Validator\Constraints\NotBlank;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductCollection::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();

        yield ImageField::new('imageUrl', t('Image'))
            ->setBasePath("images/collections")
            ->setUploadDir("/public/images/collections")
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired($pageName === Crud::PAGE_NEW)
        ;
        yield TextField::new('name', t('Name'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 128),
            ])
        ;
        yield TextareaField::new('description', t('Description'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 30),
            ])
            ->renderAsHtml()->hideOnIndex()
        ;
        yield TextField::new('buttonText', t('Button text'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
            ])
        ;
        yield TextField::new('buttonLink', t('Button link'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(max: 255),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
        ;
        yield BooleanField::new('isMegaMenu', t('Mega menu'));

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
