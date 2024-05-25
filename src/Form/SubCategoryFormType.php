<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SubCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use function Symfony\Component\Translation\t;

class SubCategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                //'purify_html' => true,
                'empty_data' => '',
                'help' => t('Keep your category names under 10 characters. Write a name that describes the content of the topic. Contextualize for your product..'),
            ])
            ->add('color', ChoiceType::class, [
                'label' => t('Color :'),
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Default' => 'dark', 
                    'Primary' => 'primary', 
                    'Secondary' => 'secondary', 
                    'Success' => 'success', 
                    'Danger' => 'danger',
                    'Warning' => 'warning', 
                    'Info' => 'info', 
                ],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
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
