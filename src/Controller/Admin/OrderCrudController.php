<?php

namespace App\Controller\Admin;

use App\Entity\Shop\Order;
use App\Repository\Shop\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\Translation\t;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Controller\Admin\Field\OrderStateField;
use App\Controller\Admin\Field\OrderDetailField;
use App\Controller\Admin\Field\OrderStatusField;
use App\Controller\Admin\Traits\DetailOnlyTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Workflow\WorkflowInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Controller\Admin\Field\OrderIsCompletedField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    use DetailOnlyTrait;

    public function __construct(
        private readonly WorkflowInterface $orderStateMachine
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', t('Order No'))->onlyOnIndex();
        yield TextField::new('ref', t('Reference product'));
        yield DateTimeField::new('createdAt', t('Order date'));
        yield IntegerField::new('totalPrice', t('Total price'));
        yield OrderStatusField::new('status', t('Status'));
        yield OrderStateField::new('state', t('Status'))->hideOnForm();

        if (Crud::PAGE_INDEX === $pageName) {
            yield BooleanField::new('isCompleted', t('Completed'));
        } else {
            yield OrderIsCompletedField::new('isCompleted', t('Completed'))->onlyOnDetail();
        }

        yield OrderDetailField::new('orderDetails', t('Command details'))->onlyOnDetail();
        yield AssociationField::new('coupon', t('Coupon'))->onlyOnDetail();

        yield FormField::addPanel(t('Client'))->onlyOnDetail();
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
        /*
        $orderStateMachine = $this->orderStateMachine;
        $actionCallback = function (Action $action) use ($orderStateMachine) {
            return $action->displayIf(
                fn (Order $order) => $orderStateMachine->getMarking($order)->has("cart")
            );
        };*/

        $isCompleted = Action::new('isCompleted', t('Mark as delivered'))
            ->addCssClass("text-info")
            ->displayAsLink()
            ->linkToRoute('admin_dashboard_order_completed', fn (Order $order) => ['id' => $order->getId()])
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $isCompleted)
            ->add(Crud::PAGE_DETAIL, $isCompleted)

            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)

            //->update(Crud::PAGE_DETAIL, Action::DELETE, $actionCallback)
            //->update(Crud::PAGE_INDEX, Action::DELETE, $actionCallback)
            //->update(Crud::PAGE_DETAIL, Action::EDIT, $actionCallback)
            //->update(Crud::PAGE_INDEX, Action::EDIT, $actionCallback)
        ;
    }

    #[Route(path: '/%website_admin_dashboard_path%/order/{id}/completed', name: 'admin_dashboard_order_completed', methods: ['GET'])]
    public function IsCompleted(
        int $id,
        TranslatorInterface $translator,
        OrderRepository $orderRepository, 
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        $order = $orderRepository->find($id);
        $order->setCompleted(true);
        $em->flush();

        $this->addFlash('success', $translator->trans('Modification made successfully.'));

        return $this->redirect(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->setEntityId($order->getId())
                ->generateUrl()
        );
    }
}
