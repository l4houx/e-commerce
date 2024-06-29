<?php

namespace App\Form\Shop;

use App\Entity\Shop\Coupon;
use App\Repository\Shop\CouponRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

use function Symfony\Component\Translation\t;

#[AsEntityAutocompleteField]
class CouponAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('Coupon'),
            'class' => Coupon::class,
            'placeholder' => t('Choose a coupon'),
            'choice_label' => 'code',
            'required' => false,
            'constraints' => [
                new NotBlank(['groups' => ['user']]),
            ],
            'query_builder' => function (CouponRepository $couponRepository) {
                return $couponRepository->createQueryBuilder('c');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
