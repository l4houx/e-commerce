<?php

namespace App\Form\Shop;

use App\Entity\Shop\Product;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Vich\UploaderBundle\Form\Type\VichImageType;

use function Symfony\Component\Translation\t;

class ProductUpdateFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => 'scale',
                'label' => t('Main product image'),
                'help' => t('Choose the right image to represent your product (We recommend using at least a 1200x600px (2:1 ratio) image )'),
                'translation_domain' => 'messages',
                'attr' => ['class' => 'mb-3'],
            ])
            ->add('images', CollectionType::class, [
                'label' => t('Images gallery'),
                'entry_type' => ProductImageFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                // 'prototype' => true,
                'required' => false,
                'by_reference' => false,
                'entry_options' => ['label' => false],
                'attr' => [
                    // 'data-controller' => 'form-collection',
                    'data-form-collection-add-label-value' => t('Add an image'),
                    'data-form-collection-delete-label-value' => t('Remove an image'),
                ],
                'help' => t('Add other images that represent your product to be displayed as a gallery'),
                'error_bubbling' => false,
            ])
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            /*
            ->add('slug', TextType::class, [
                'label' => t('Slug'),
                'empty_data' => '',
                'required' => false,
                'help' => t('Field must contain an unique value.'),
            ])
            */
            ->add('content', TextareaType::class, [
                'label' => t('Content'),
                'required' => false,
                'empty_data' => '',
                'attr' => ['placeholder' => '', 'rows' => 6],
                'help' => t(''),
            ])
            ->add('price', MoneyType::class, options: [
                'label' => t('Price'),
                'divisor' => 100,
                'constraints' => [
                    new Positive(
                        message: t('The price cannot be negative')
                    ),
                ],
            ])
            /*
            ->add('stock', options: [
                'label' => t('Units in stock'),
            ])
            */
            ->add('subCategories', SubCategoriesAutocompleteField::class)
            ->add('brand', BrandAutocompleteField::class)
            ->add('metaTitle', TextType::class, [
                'label' => t('Meta title'),
                'required' => false,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => t('Meta description'),
                'required' => false,
                'empty_data' => '',
                'attr' => ['placeholder' => '', 'rows' => 6],
                'help' => t(''),
            ])
            /*
            ->add('isActive', ChoiceType::class, [
                'label' => t('Active'),
                'required' => false,
                'multiple' => false,
                'expanded' => true,
                'choices' => ['Enable' => true, 'Disable' => false],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
            ])
            */
            // ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => ['update'],
        ]);
    }
}
