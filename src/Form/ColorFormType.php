<?php

namespace App\Form;

use App\Entity\Color;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
                'attr' => ['min' => 0, 'max' => 1, 'step' =>1 ],
            ])
            ->add('name', TextType::class, [
                'label' => t('Name'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('isActive', ChoiceType::class, [
                'label' => t('Active'),
                'required' => false,
                'multiple' => false,
                'expanded' => true,
                'choices' => ['Enable' => 1, 'Disable' => 0],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
            ])
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
