<?php

namespace App\Form\Shop;

use App\Entity\Shop\Order;
use App\Entity\Shop\Shipping;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class OrderFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => t('First name'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 2,
                        'max' => 20,
                        'groups' => ['user', 'pos']]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => t('Last name'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 2,
                        'max' => 20,
                        'groups' => ['user', 'pos']]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => t('Email address'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new Email(['groups' => ['user']]),
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 5,
                        'max' => 180,
                        'groups' => ['user']]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => t('Phone'),
                // 'purify_html' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                    new Length([
                        'min' => 10,
                        'max' => 20,
                        'groups' => ['user']]),
                ],
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
            ->add('countrycode', EntityType::class, [
                'label' => t('Country'),
                'class' => Shipping::class,
                // 'placeholder' => t('Choose a country'),
                'choice_label' => 'name',
                'required' => true,
                // 'autocomplete' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['user']]),
                ],
            ])
            // ->add('countrycode', CountryAutocompleteField::class)
            ->add('isPayOnDelivery', CheckboxType::class, [
                'label' => t('Pay On Delivery'),
                'required' => false,
                'help' => t('If you wish to pay on delivery check the box, otherwise click on "place order" for online payment.'),
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'validation_groups' => ['user', 'pos'],
        ]);
    }
}
