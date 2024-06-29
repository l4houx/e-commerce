<?php

namespace App\Form\Shop;

use App\Entity\Shop\Size;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class SizeFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            'data_class' => Size::class,
        ]);
    }
}
