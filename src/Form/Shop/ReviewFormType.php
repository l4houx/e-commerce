<?php

namespace App\Form\Shop;

use App\Entity\Shop\Review;
use App\Form\FormListenerFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class ReviewFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => ['placeholder' => t('Your review name *')],
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'label' => false,
                'empty_data' => '',
                'required' => false,
                'attr' => ['placeholder' => t('Slug')],
                'help' => t('Field must contain an unique value.'),
            ])
            ->add('rating', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => ['★★★★★ (5/5)' => 5, '★★★★☆ (4/5)' => 4, '★★★☆☆ (3/5)' => 3, '★★☆☆☆ (2/5)' => 2, '★☆☆☆☆ (1/5)' => 1],
                'label_attr' => ['class' => 'radio-custom radio-inline'],
                'help' => t('Your rating (out of 5 stars) *'),
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'required' => true,
                'empty_data' => '',
                'attr' => ['placeholder' => t('Your content *'), 'rows' => 3],
                'help' => t(''),
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->slug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
