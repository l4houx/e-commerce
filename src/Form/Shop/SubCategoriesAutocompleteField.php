<?php

namespace App\Form\Shop;

use App\Entity\Shop\SubCategory;
use App\Repository\Shop\SubCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

use function Symfony\Component\Translation\t;

#[AsEntityAutocompleteField]
class SubCategoriesAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('Sub Category'),
            'class' => SubCategory::class,
            'placeholder' => t('Choose a sub category'),
            'choice_label' => 'name',
            'multiple' => true,
            'query_builder' => function (SubCategoryRepository $subCategoryRepository) {
                return $subCategoryRepository->createQueryBuilder('s');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
