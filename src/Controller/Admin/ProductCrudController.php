<?php

namespace App\Controller\Admin;

use App\Entity\Shop\Product;
use App\Entity\Traits\HasRoles;
use App\Form\Shop\FeatureValueFormType;
use App\Form\Shop\ProductImageFormType;
use App\Form\Shop\SubCategoryFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Vich\UploaderBundle\Form\Type\VichFileType;

use function Symfony\Component\Translation\t;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab(t('Information'));
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('ref', t('Reference product'))->onlyOnDetail();

        yield TextField::new('name', t('Name'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 128),
            ])
        ;
        yield SlugField::new('slug', t('URL'))
            ->setTargetFieldName('name')
            ->hideOnIndex()
        ;
        yield TextField::new('tags', t('Tags'))
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'class' => 'tags-input',
            ])
            ->setHelp('')
        ;

        if (Crud::PAGE_NEW === $pageName) {
            yield TextEditorField::new('content', t('Content'))
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                    new Length(['min' => 2000]),
                ])
                ->hideOnIndex()
            ;
        } else {
            yield TextareaField::new('content', t('Content'))
                ->setFormTypeOption('constraints', [
                    new NotBlank(),
                    new Length(['min' => 2000]),
                ])
                ->renderAsHtml()->hideOnIndex()
            ;
        }
        yield TextEditorField::new('moreContent', t('More content'))->hideOnIndex();
        yield TextEditorField::new('additionalInformation', t('Additional information'))->hideOnIndex();

        yield IntegerField::new('price', t('Price'))
            ->setFormTypeOption('constraints', [
                new Positive(),
                new LessThan(1001),
            ])
            ->hideOnIndex()
        ;
        yield IntegerField::new('salePrice', t('Sale Price'))
            ->setFormTypeOption('constraints', [
                new Positive(),
                new LessThan(1001),
            ])
            ->hideOnIndex()
        ;
        yield IntegerField::new('stock', t('Stock'))
            ->setFormTypeOption('constraints', [
                new PositiveOrZero(message: 'Stock cannot be negative'),
            ])
            ->formatValue(function ($value) {
                return $value < 10 ? sprintf('%d **LOW STOCK**', $value) : $value;
            })
            ->hideOnIndex()
        ;
        yield IntegerField::new('views', t('Views'))->hideOnForm()->onlyOnDetail();

        yield FormField::addTab(t('Product image'));
        yield ImageField::new('imageName', t('Image'))
            ->setUploadDir('public/uploads/product/')
            ->setBasePath('/uploads/product')
            ->hideOnForm()
        ;
        yield TextField::new('imageFile')->setFormType(VichFileType::class)->onlyOnForms();
        yield CollectionField::new('images')
            ->setEntryType(ProductImageFormType::class)
            ->allowDelete()
            ->allowAdd()
            ->hideOnIndex()
        ;

        yield FormField::addTab(t('SEO'))->hideOnIndex();
        yield TextField::new('metaTitle', t('Title'))->hideOnIndex();
        yield TextareaField::new('metaDescription', t('Description'))->renderAsHtml()->hideOnIndex();

        yield FormField::addTab(t('Actived'));
        yield BooleanField::new('isFeaturedProduct', t('Featured product'));
        yield BooleanField::new('isBestSelling', t('Best selling'));
        yield BooleanField::new('isNewArrival', t('New arrival'));
        yield BooleanField::new('isSpecialOffer', t('Special offer'));
        yield BooleanField::new('isAvailable', t('Available'));
        yield BooleanField::new('isOnline', t('Published'));
        yield BooleanField::new('enablereviews', t('Enable reviews'))->onlyOnDetail();

        yield FormField::addTab(t('Social Networks'))->hideOnIndex();
        yield UrlField::new('externallink', t('External link'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
            ->hideOnIndex()
        ;
        yield UrlField::new('website', t('Website'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
            ->hideOnIndex()
        ;
        yield EmailField::new('email', t('Email'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 50]),
            ])
            ->hideOnIndex()
        ;
        yield TelephoneField::new('phone', t('Phone'))
            ->setFormTypeOption('constraints', [
                new Length(['min' => 10]),
            ])
            ->hideOnIndex()
        ;
        yield UrlField::new('youtubeurl', t('Youtube'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
                new Regex(
                    pattern: '^(http|https):\/\/(www\.youtube\.com|www\.dailymotion\.com)\/?',
                    // match: true,
                    message: 'The URL must match the URL of a Youtube or DailyMotion video',
                ),
            ])->hideOnIndex()
        ;
        yield UrlField::new('twitterurl', t('Twitter'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
            ->hideOnIndex()
        ;
        yield UrlField::new('instagramurl', t('Instagram'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
            ->hideOnIndex()
        ;
        yield UrlField::new('facebookurl', t('Facebook'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
            ->hideOnIndex()
        ;
        yield UrlField::new('googleplusurl', t('Google plus'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
            ->hideOnIndex()
        ;
        yield UrlField::new('linkedinurl', t('Linkedin'))
            ->setFormTypeOption('constraints', [
                new Length(['max' => 255]),
                new Url(
                    message: 'This value is not a valid URL.',
                    protocols: ['http', 'https'],
                ),
            ])
            ->hideOnIndex()
        ;

        yield FormField::addTab(t('Features'))->hideOnIndex();
        yield CollectionField::new('features', t('Features'))
            ->setEntryIsComplex(true)
            ->setEntryType(FeatureValueFormType::class)
            ->setTemplatePath('admin/field/features.html.twig')
            ->hideOnIndex()
        ;
        yield AssociationField::new('brand', t('Brand'))
            ->setCrudController(BrandCrudController::class)
            ->autocomplete()
            ->hideOnIndex()
        ;
        yield AssociationField::new('subCategories', t('Sub categories'))
            ->setCrudController(SubCategoryCrudController::class)->hideOnIndex()
        ;
        yield ArrayField::new('subCategories', t('Sub categories'))->hideOnIndex();

        /*
        yield CollectionField::new('subCategories', t('Sub categories'))
            ->setEntryType(SubCategoryFormType::class)
            ->allowDelete()
            ->allowAdd()
        ;
        */

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('Product'))
            ->setEntityLabelInPlural(t('Products'))
            ->setDefaultSort(['createdAt' => 'DESC', 'name' => 'ASC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm:ss')
            ->setSearchFields(['name'])
            ->setAutofocusSearch()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', t('Name')))
            ->add(DateTimeFilter::new('createdAt', t('Creation date')))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewProduct = Action::new('viewProduct', t('See the product'))
            ->setHtmlAttributes([
                'target' => '_blank',
            ])
            ->linkToCrudAction('viewProduct')
        ;

        return $actions
            ->add(Crud::PAGE_EDIT, $viewProduct)
            ->add(Crud::PAGE_INDEX, $viewProduct)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            // ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }

    public function viewProduct(AdminContext $context): Response
    {
        /** @var Product $entity */
        $entity = $context->getEntity()->getInstance();

        return $this->redirectToRoute('shop_product', [
            'slug' => $entity->getSlug(),
        ]);
    }
}
