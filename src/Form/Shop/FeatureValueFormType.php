<?php

namespace App\Form\Shop;

use App\Entity\Shop\Feature;
use App\Entity\Shop\FeatureValue;
use App\Entity\Shop\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class FeatureValueFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', TextType::class, [
                'label' => t('Value'),
                'empty_data' => '',
            ])
            ->add('product', EntityType::class, [
                'label' => t('Product'),
                'class' => Product::class,
                'choice_label' => 'name',
            ])
            ->add('feature', EntityType::class, [
                'label' => t('Feature'),
                'class' => Feature::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FeatureValue::class,
        ]);
    }
}
