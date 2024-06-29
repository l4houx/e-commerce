<?php

namespace App\Form\Shop;

use App\Entity\Shop\Color;
use App\Form\FormListenerFactory;
use App\Form\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class ColorFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hex', ColorType::class, [
                'label' => t('Hex'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('displayInSearch', IntegerType::class, [
                'label' => t('Display in search'),
                'required' => false,
                'attr' => ['min' => 0, 'max' => 1, 'step' => 1],
            ])
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('isOnline', SwitchType::class, ['label' => t('Online')])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Color::class,
        ]);
    }
}
