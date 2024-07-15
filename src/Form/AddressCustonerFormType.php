<?php

namespace App\Form;

use App\Entity\Shop\AddressCustoner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use function Symfony\Component\Translation\t;

class AddressCustonerFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addressType', ChoiceType::class, [
                'required' => true,
                'label' => t('Address Type'),
                'choices' => ['Billing' => 'Billing', 'Shipping' => 'Shipping'],
            ])
            ->add('name', TextType::class, [
                'label' => t('Name'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 4,
                        'max' => 128,
                    ]),
                ],
            ])
            ->add('clientName', TextType::class, [
                'label' => t('Client name'),
                // 'purify_html' => true,
                'required' => true,
            ])
            ->add('moreDetails', TextareaType::class, [
                'label' => t('More Details'),
                'required' => false,
                'empty_data' => '',
                'attr' => ['placeholder' => '', 'rows' => 6],
                'help' => t(''),
            ])
            ->add('street', TextType::class, [
                'label' => t('Street'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 4,
                        'max' => 50,
                        'groups' => ['user']]),
                ],
            ])
            ->add('street2', TextType::class, [
                'label' => t('Street 2 (Optional)'),
                // 'purify_html' => true,
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'groups' => ['user'],
                    ]),
                ],
            ])
            ->add('postalcode', TextType::class, [
                'label' => t('Postal code'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 5,
                        'max' => 5,
                        'groups' => ['user']]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => t('City'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 4,
                        'max' => 50,
                        'groups' => ['user']]),
                ],
            ])
            ->add('countrycode', CountryType::class, [
                'label' => t('Country'),
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddressCustoner::class,
        ]);
    }
}
