<?php

namespace App\Form;

use App\Entity\Shop\Shipping;
use Symfony\Component\Form\AbstractType;
use App\Repository\Shop\ShippingRepository;
use function Symfony\Component\Translation\t;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class CountryAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('Country'),
            'class' => Shipping::class,
            'placeholder' => t('Choose a country'),
            'choice_label' => 'name',
            'required' => true,
            'constraints' => [
                new NotBlank(['groups' => ['user']]),
            ],
            'query_builder' => function (ShippingRepository $shippingRepository) {
                return $shippingRepository->createQueryBuilder('s');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
