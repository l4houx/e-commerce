<?php

namespace App\Form;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

use function Symfony\Component\Translation\t;

#[AsEntityAutocompleteField]
class BrandAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('Brand'),
            'class' => Brand::class,
            'placeholder' => t('Choose a brand'),
            'choice_label' => 'name',
            'query_builder' => function (BrandRepository $brandRepository) {
                return $brandRepository->createQueryBuilder('b');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
