<?php

namespace App\Form\Shop;

use App\DataTransferObject\SavFormDTO;
use App\Entity\Shop\Order;
use App\Entity\Shop\OrderDetail;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

use function Symfony\Component\Translation\t;

class SavFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderDetail', EntityType::class, [
                'label' => t('Product concerned'),
                'class' => OrderDetail::class,
                'choice_label' => fn (OrderDetail $orderDetail) => sprintf(
                    '%s - %s - %s',
                    $orderDetail->getOrder()->getCreatedAt()->format('d/m/Y'),
                    $orderDetail->getProduct()->getName(),
                    $orderDetail->getProduct()->getBrand()->getName()
                ),
                'query_builder' => fn (EntityRepository $repository) => $repository->createQueryBuilder('od')
                    ->where('od.order = :order')
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
            ->add('content', TextareaType::class, [
                'label' => t('Accurate content of the fault'),
                'attr' => ['placeholder' => 'Content here', 'rows' => 4],
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
