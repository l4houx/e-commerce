<?php

namespace App\Form\Shop;

use App\Entity\Shop\SubCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class SubCategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
                'help' => t('Keep your category names under 10 characters. Write a name that describes the content of the topic. Contextualize for your product..'),
            ])
            ->add('color', ColorType::class, [
                'label' => t('Color'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('category', CategoryAutocompleteField::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
        ]);
    }
}
