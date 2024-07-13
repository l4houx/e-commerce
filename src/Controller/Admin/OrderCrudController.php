<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\OrderDetailField;
use App\Controller\Admin\Field\OrderIsCompletedField;
use App\Controller\Admin\Field\OrderStatusField;
use App\Controller\Admin\Traits\DetailOnlyTrait;
use App\Entity\Shop\Order;
use App\Repository\Shop\OrderRepository;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

class OrderCrudController extends AbstractCrudController
{
    // use DetailOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly OrderRepository $orderRepository,
        private readonly SettingService $settingService,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Order information'))->onlyOnDetail();
        yield IdField::new('id', t('Order No.'))->onlyOnIndex();
        yield TextField::new('ref', t('Reference product'));
        yield DateTimeField::new('createdAt', t('Order date'));
        yield IntegerField::new('totalPrice', t('Total price'));
        yield OrderStatusField::new('status', t('Status'))->hideOnForm();
        yield OrderIsCompletedField::new('isCompleted', t('Completed'))->hideOnForm();
        yield OrderDetailField::new('orderDetails', t('Command details'))->onlyOnDetail();
        yield AssociationField::new('coupon', t('Coupon'))->onlyOnDetail();

        yield FormField::addPanel(t('Customer information'))->onlyOnDetail();
        yield TextField::new('firstname', t('Firstname'))->onlyOnDetail();
        yield TextField::new('lastname', t('Lastname'))->onlyOnDetail();
        yield EmailField::new('email', t('Email'))->onlyOnDetail();
        yield TextField::new('phone', t('Phone Number'))->onlyOnDetail();

        yield FormField::addPanel(t('Address'))->onlyOnDetail();
        yield TextField::new('street', t('Address'))->onlyOnDetail();
        yield TextField::new('street2', t('Additional address'))->onlyOnDetail();
        yield TextField::new('postalcode', t('Postal code'))->onlyOnDetail();
        yield TextField::new('city', t('City'))->onlyOnDetail();
        yield AssociationField::new('countrycode', t('Country'))->onlyOnDetail();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Order account'))
            ->setEntityLabelInPlural(t('Orders accounts'))
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setDateFormat('dd/MM/YYYY')
            ->setDateTimeFormat('dd/MM/YYYY HH:mm')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(DateTimeFilter::new('createdAt', t('Creation date')));
    }

    public function configureActions(Actions $actions): Actions
    {
        $isCompleted = Action::new('isCompleted', t('Mark as delivered'))
            ->addCssClass('text-info')
            ->displayAsLink()
            ->displayIf(fn (Order $order) => $order->isPaid() && $order->isCompleted() && $order->setStatus(1) != true)
            ->linkToRoute('admin_order_completed', fn (Order $order) => ['id' => $order->getId()])
        ;

        $validate = Action::new('validate', t('Validate'))
            ->addCssClass('text-success')
            ->displayAsLink()
            ->displayIf(fn (Order $order) => $order->isPaid() && $order->isCompleted() && $order->setStatus(1))
            ->linkToRoute('admin_order_validate', fn (Order $order) => ['id' => $order->getId()])
        ;

        $cancel = Action::new('cancel', t('Cancel'))
            ->addCssClass('text-warning')
            ->displayAsLink()
            ->displayIf(fn (Order $order) => $order->setIsPaid(0) && $order->setIsCompleted(0) && $order->setStatus(0))
            ->linkToRoute('admin_order_cancel', fn (Order $order) => ['id' => $order->getId()])
        ;

        $resendConfirmationEmail = Action::new('resendConfirmationEmail', t('Resend Confirmation Email'))
            ->addCssClass('text-primary')
            ->displayAsLink()
            ->displayIf(fn (Order $order) => $order->isPaid() && $order->isCompleted() && $order->setStatus(1))
            ->linkToRoute('admin_order_resend_confirmation_email', fn (Order $order) => ['id' => $order->getId()])
        ;

        $actions
            ->add(Crud::PAGE_INDEX, $isCompleted)
            ->add(Crud::PAGE_DETAIL, $isCompleted)
            ->add(Crud::PAGE_INDEX, $validate)
            ->add(Crud::PAGE_DETAIL, $validate)
            ->add(Crud::PAGE_INDEX, $cancel)
            ->add(Crud::PAGE_DETAIL, $cancel)
            ->add(Crud::PAGE_INDEX, $resendConfirmationEmail)
            ->add(Crud::PAGE_DETAIL, $resendConfirmationEmail)

            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ;

        return $actions;
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/completed', name: 'admin_order_completed', methods: ['GET'])]
    public function isCompleted(int $id): Response
    {
        $order = $this->em->getRepository(Order::class)->find($id);
        $order->setIsCompleted(true);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Modification made successfully.'));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->setEntityId($order->getId())
                ->generateUrl()
        );
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/validate', name: 'admin_order_validate', methods: ['GET'])]
    public function validate(int $id): Response
    {
        $order = $this->settingService->getOrders(['id' => $id, 'status' => 0])->getQuery()->getOneOrNullResult();

        $this->addFlash('success', $this->translator->trans('The order has been successfully validated.'));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->setEntityId($order->getId())
                ->generateUrl()
        );
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/cancel', name: 'admin_order_cancel', methods: ['GET'])]
    public function cancel(Request $request, int $id): Response
    {
        $order = $this->em->getRepository(Order::class)->find($id);
        if (!$order) {
            $this->addFlash('danger', $this->translator->trans('The order can not be found'));
            return $this->redirect($request->headers->get('referer'));
        }

        if (0 != $order->getStatus() && 1 != $order->getStatus()) {
            $this->addFlash('danger', $this->translator->trans('The order status must be paid or awaiting payment.'));
            return $this->redirect($request->headers->get('referer'));
        }

        $this->settingService->handleCanceledPayment($order->getRef(), $request->query->get('note'));

        $this->addFlash('success', $this->translator->trans('The order has been permanently canceled.'));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->setEntityId($order->getId())
                ->generateUrl()
        );
    }

    #[Route(path: '/%website_admin_path%/manage-orders/{id}/resend-confirmation-email', name: 'admin_order_resend_confirmation_email', methods: ['GET'])]
    public function resendConfirmationEmail(Request $request, int $id): Response
    {
        $order = $this->em->getRepository(Order::class)->find($id);
        if (!$order) {
            $this->addFlash('danger', $this->translator->trans('The order can not be found'));
            return $this->redirect($request->headers->get('referer'));
        }

        $this->settingService->sendOrderConfirmationEmail($order, $request->query->get('email'));

        $this->addFlash('success', $this->translator->trans('The confirmation email has been resent to').' '.$request->query->get('email'));

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->setEntityId($order->getId())
                ->generateUrl()
        );
    }
}
