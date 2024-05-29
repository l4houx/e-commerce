<?php

namespace App\Form;

use App\Entity\Coupon;
use App\Entity\Order;
use App\Entity\Shipping;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('email')
            ->add('street')
            ->add('street2')
            ->add('postalcode')
            ->add('city')
            ->add('reference')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('countrycode', EntityType::class, [
                'class' => Shipping::class,
                'choice_label' => 'id',
            ])
            ->add('coupon', EntityType::class, [
                'class' => Coupon::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
