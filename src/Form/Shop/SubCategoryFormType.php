<?php

namespace App\Form\Shop;

use App\Entity\Shop\Category;
use App\Entity\Shop\SubCategory;
use Symfony\Component\Form\AbstractType;
use App\Repository\Shop\CategoryRepository;
use function Symfony\Component\Translation\t;
use App\Repository\Shop\SubCategoryRepository;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

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
            //->add('category', CategoryAutocompleteField::class)
            ->add('category', EntityType::class, [
                'label' => t('Categorie'),
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => t('Choose a category'),
                'autocomplete' => true,
                'required' => false,
                'multiple' => false,
                'attr' => ['data-limit' => 1],
                'help' => t('Make sure to select right category to let the users find it quickly'),
                'query_builder' => function (CategoryRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
        ]);
    }
}
