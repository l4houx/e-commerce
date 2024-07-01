<?php

namespace App\Controller\Admin;

use App\Entity\Shop\Product;
use App\Form\Shop\FeatureValueFormType;
use App\Form\Shop\ProductImageFormType;
use Liip\ImagineBundle\Form\Type\ImageType;
use App\Controller\Admin\UserCrudController;
use function Symfony\Component\Translation\t;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;
use App\Controller\Admin\CategoryCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotNull;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Validator\Constraints\GreaterThan;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    /*public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Information'));
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('ref', t('Reference product'));

        yield TextField::new('name', t('Name'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 128),
            ])
        ;
        yield SlugField::new('slug')
            ->setTargetFieldName('name')
            ->onlyOnDetail()
            ->hideOnForm()
        ;
        yield TextField::new('tags', t('Tags'))
            ->onlyOnDetail()
            ->setFormTypeOption('attr', [
                'class' => 'tags-input',
            ])
            ->setHelp('')
        ;

        if (Crud::PAGE_NEW === $pageName) {
            yield TextEditorField::new('content', t('Content'))
                ->setFormTypeOption('constraints', [
                    new Length(['min' => 2000]),
                ])
                ->hideOnIndex()
            ;
        } else {
            yield TextareaField::new('content', t('Content'))
                ->setFormTypeOption('constraints', [
                    new Length(['min' => 2000]),
                ])
                ->renderAsHtml()->hideOnIndex()
            ;
        }

        yield IntegerField::new('price', t('Price'))
            ->setFormTypeOption('constraints', [
                new Positive(),
                new LessThan(1001),
            ])
        ;
        yield PercentField::new('tax', t('Tax'))
            ->setFormTypeOption('constraints', [
                new GreaterThan(0),
            ])
        ;
        yield IntegerField::new('stock', t('Stock'))
        ->setFormTypeOption('constraints', [
                new PositiveOrZero(message: 'Stock cannot be negative'),
            ])->onlyOnDetail()
        ;
        yield IntegerField::new('views', t('Views'))->hideOnForm()->onlyOnDetail();

        yield FormField::addPanel(t('Image'));
        yield ImageField::new('imageName')
            ->setUploadDir('public/uploads/product/')
            ->setBasePath('/uploads/product')
            ->hideOnForm()
        ;
        yield TextField::new('imageFile')->setFormType(VichFileType::class)->onlyOnForms();
        yield IntegerField::new('imageSize', t('Image size'))->onlyOnDetail();
        yield TextField::new('imageMimeType', t('Image mime type'))->onlyOnDetail();
        yield TextField::new('imageOriginalName', t('Image original name'))->onlyOnDetail();
        yield ArrayField::new('imageDimensions', t('Image dimensions'))->onlyOnDetail();

        yield FormField::addPanel(t('SEO'))->onlyOnDetail();
        yield TextField::new('metaTitle', t('Title'))->onlyOnDetail();
        yield TextareaField::new('metaDescription', t('Description'))->renderAsHtml()->onlyOnDetail();

        yield FormField::addPanel(t('Actived'));
        yield BooleanField::new('isOnline', t('Published'));
        yield BooleanField::new('enablereviews', t('Enable reviews'))
            ->setFormTypeOption('constraints', [
                new NotNull(groups: ['create', 'update']),
            ])->onlyOnDetail()
        ;

        yield FormField::addPanel(t('Association'));
        yield CollectionField::new('features', t('Features'))
            ->setEntryIsComplex(true)
            ->setEntryType(FeatureValueFormType::class)
            ->setTemplatePath('admin/field/features.html.twig')
        ;
        yield AssociationField::new('brand', t('Brand'))
            ->setCrudController(BrandCrudController::class)
            ->autocomplete()
            ->onlyOnDetail()
        ;
        yield AssociationField::new('subCategories', t('Sub categories'))
            ->setCrudController(SubCategoryCrudController::class)
        ;

        /*
        yield CollectionField::new('images')
            ->setEntryType(ProductImageFormType::class)
            ->allowDelete()
            ->allowAdd()
        ;/

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deletion date'))->hideOnForm()->onlyOnDetail();
    }*/

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
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
        $viewProduct = Action::new('viewProduct', t("See the product"))
            ->setHtmlAttributes([
                'target' => '_blank'
            ])
            ->linkToCrudAction('viewProduct')
        ;

        return $actions
            ->add(Crud::PAGE_EDIT, $viewProduct)
            ->add(Crud::PAGE_INDEX, $viewProduct)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            //->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }

    public function viewProduct(AdminContext $context): Response
    {
        /** @var Product $entity */
        $entity = $context->getEntity()->getInstance();

        return $this->redirectToRoute('shop_product', [
            'slug' => $entity->getSlug()
        ]);
    }
}
