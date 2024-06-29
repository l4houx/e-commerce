<?php

namespace App\Form\Shop;

use App\Entity\Shop\Line;
use App\Entity\Shop\Order;
use Doctrine\ORM\EntityRepository;
use App\DataTransferObject\SavFormDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use function Symfony\Component\Translation\t;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SavFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line', EntityType::class, [
                'label' => t('Product concerned'),
                'class' => Line::class,
                'choice_label' => fn (Line $line) => sprintf(
                    '%s - %s - %s',
                    $line->getOrder()->getCreatedAt()->format('d/m/Y'),
                    $line->getProduct()->getName(),
                    $line->getProduct()->getBrand()->getName()
                ),
                'query_builder' => fn (EntityRepository $repository) => $repository->createQueryBuilder('l')
                    ->where('l.order = :order')
                    ->setParameter('order', $options['order']),
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('_attachments', DropzoneType::class, [
                'label' => t('Attachments'),
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'data-controller' => 'sav',
                    'placeholder' => t('Upload your attachments'),
                ],
            ])
            ->add('attachments', CollectionType::class, [
                'label' => false,
                'entry_type' => HiddenType::class,
                'allow_add' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => t('Accurate description of the fault'),
                'attr' => ['placeholder' => 'Description here', 'rows' => 4],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => t('Comments'),
                'attr' => ['placeholder' => 'Leave a comment here', 'rows' => 4],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('order');
        $resolver->setAllowedTypes('order', Order::class);
        $resolver->setDefault('data_class', SavFormDTO::class);
    }
}
