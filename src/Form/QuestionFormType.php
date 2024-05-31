<?php

namespace App\Form;

use App\Entity\Question;
use App\Form\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class QuestionFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $formListenerFactory)
    {
        // code...
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, [
                'label' => t('Question'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
            ])
            ->add('answer', TextareaType::class, [
                'label' => t('Answer'),
                'required' => true,
                // 'purify_html' => true,
                'empty_data' => '',
                'attr' => ['class' => 'wysiwyg', 'placeholder' => '', 'rows' => 6],
                'help' => t(''),
            ])
            ->add('isOnline', SwitchType::class, ['label' => t('Online')])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
