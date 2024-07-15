<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Rules;
use App\Entity\Comment;
use App\Entity\PostType;
use App\Entity\Question;
use App\Entity\Shop\Size;
use App\Entity\Shop\Brand;
use App\Entity\Shop\Color;
use App\Entity\Shop\Order;
use App\Entity\Shop\Coupon;
use App\Entity\Shop\Review;
use App\Entity\Testimonial;
use App\Entity\PostCategory;
use App\Entity\Shop\Feature;
use App\Entity\Shop\Product;
use App\Entity\User\Manager;
use App\Entity\HelpCenterFaq;
use App\Entity\Settings\Page;
use App\Entity\Shop\Category;
use App\Entity\Shop\Shipping;
use App\Entity\Tickets\Level;
use App\Entity\User\Customer;
use App\Entity\Company\Client;
use App\Entity\Company\Member;
use App\Entity\Tickets\Status;
use App\Entity\Tickets\Ticket;
use App\Entity\Traits\HasRoles;
use App\Entity\Settings\Setting;
use App\Entity\Shop\SubCategory;
use App\Entity\User\SalesPerson;
use App\Entity\HelpCenterArticle;
use App\Entity\Settings\Currency;
use App\Entity\User\Collaborator;
use App\Entity\HelpCenterCategory;
use App\Entity\SuperAdministrator;
use App\Entity\Company\Organization;
use App\Entity\Shop\AddressCustoner;
use App\Entity\Shop\ProductCollection;
use App\Entity\Settings\AppLayoutSetting;
use App\Entity\Settings\HomepageHeroSetting;
use function Symfony\Component\Translation\t;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Entity\Tickets\Response as TicketsResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route(path: '/%website_admin_path%', name: 'admin_index')]
    #[IsGranted(HasRoles::TEAM)]
    public function index(): Response
    {
        $controller = $this->isGranted(HasRoles::ADMIN) ? TicketCrudController::class : ProductCrudController::class;

        $url = $this->adminUrlGenerator
            ->setController($controller)
            ->generateUrl()
        ;

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->getParameter('website_name'))
            ->setTranslationDomain('admin')
            ->setTextDirection('ltr')
            ->renderContentMaximized()
            ->renderSidebarMinimized()
            ->generateRelativeUrls()
            ->setLocales([
                'en', 'fr',
            ])
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl(t('Visit public website'), 'fas fa-undo', '/');

        yield MenuItem::linkToDashboard(t('Dashboard'), 'fa fa-home');

        if ($this->isGranted(HasRoles::ADMINAPPLICATION)) {
            yield MenuItem::section(t('General settings'));
            yield MenuItem::subMenu(t('General'), 'fa fa-wrench')->setSubItems([
                MenuItem::linkToCrud(t('Settings'), 'fas fa-cog', Setting::class),
                MenuItem::linkToCrud(t('Layout settings'), 'fas fa-images', AppLayoutSetting::class),
                MenuItem::linkToCrud(t('Heros settings'), 'fas fa-image', HomepageHeroSetting::class),
                MenuItem::linkToCrud(t('Currency settings'), 'fas fa-money-check-dollar', Currency::class),
                MenuItem::linkToCrud(t('All pages'), 'fas fa-file', Page::class),
            ]);

            yield MenuItem::section(t('Shoppings Settings'));
            yield MenuItem::subMenu(t('Shoppings'), 'fas fa-shop')->setSubItems([
                MenuItem::linkToCrud(t('Products'), 'fas fa-list', Product::class),
                MenuItem::linkToCrud(t('Collections'), 'fas fa-panorama', ProductCollection::class),
                //MenuItem::linkToCrud(t('Sliders'), 'fas fa-image', Sliders::class),
                //MenuItem::linkToCrud(t('Carriers'), 'fas fa-car', Carrier::class),
                MenuItem::linkToCrud(t('Features'), 'fa fa-bookmark', Feature::class),
                MenuItem::linkToCrud(t('Brands'), 'fas fa-b', Brand::class),
                MenuItem::linkToCrud(t('Sub Categories'), 'fab fa-delicious', SubCategory::class),
                MenuItem::linkToCrud(t('Categories'), 'fab fa-delicious', Category::class),
                MenuItem::linkToCrud(t('Sizes'), 'fas fa-file', Size::class),
                MenuItem::linkToCrud(t('Colors'), 'fas fa-file', Color::class),
                MenuItem::linkToCrud(t('Shippings'), 'fas fa-truck', Shipping::class),
                MenuItem::linkToCrud(t('Coupons'), 'fas fa-copy', Coupon::class),
                MenuItem::linkToCrud(t('Orders'), 'fas fa-shopping-cart', Order::class),
                MenuItem::linkToCrud(t('Review'), 'fas fa-star', Review::class),
                MenuItem::linkToCrud(t('Address Custoner'), 'fas fa-list', AddressCustoner::class),
            ]);

            yield MenuItem::section(t('Review Settings'));
            yield MenuItem::subMenu(t('Testimonial'), 'fas fa-star')->setSubItems([
                MenuItem::linkToCrud(t('Testimonial'), 'fas fa-star', Testimonial::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Testimonial::class)->setAction(Crud::PAGE_NEW),
            ]);

            yield MenuItem::section(t('Help Center Settings'));
            yield MenuItem::subMenu(t('Help Center'), 'fas fa-newspaper')->setSubItems([
                MenuItem::linkToCrud(t('All articles'), 'fas fa-newspaper', HelpCenterArticle::class),
                MenuItem::linkToCrud(t('All categories'), 'fab fa-delicious', HelpCenterCategory::class),
                MenuItem::linkToCrud(t('All faqs'), 'fa fa-question-circle', HelpCenterFaq::class),
                MenuItem::linkToCrud(t('All questions'), 'fa fa-question-circle', Question::class),
            ]);

            // yield MenuItem::section(t('Contact Settings'));
            // yield MenuItem::linkToCrud(t('Contact'), 'fas fa-message', Contact::class);

            yield MenuItem::section(t('Clients Settings'));
            yield MenuItem::subMenu(t('All accounts'), 'fas fa-user')->setSubItems([
                MenuItem::linkToCrud(t('Companies'), 'fa fa-building', Client::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Client::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Users'), 'fa fa-users', Customer::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Customer::class)->setAction(Crud::PAGE_NEW),
            ]);

            yield MenuItem::section(t('Members Settings'));
            yield MenuItem::subMenu(t('All accounts'), 'fas fa-user')->setSubItems([
                MenuItem::linkToCrud(t('Groups'), 'fa fa-building', Organization::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Organization::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Members'), 'fa fa-building', Member::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Member::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Managers'), 'fa fa-users', Manager::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Manager::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Sales Persons'), 'fa fa-users', SalesPerson::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', SalesPerson::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Collaborators'), 'fa fa-users', Collaborator::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Collaborator::class)->setAction(Crud::PAGE_NEW),
            ]);

            yield MenuItem::section(t('Users Settings'));
            yield MenuItem::subMenu(t('All accounts'), 'fas fa-user')->setSubItems([
                MenuItem::linkToCrud(t('Teams'), 'fa fa-user-shield', SuperAdministrator::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', SuperAdministrator::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Users'), 'fas fa-user-friends', User::class),
                MenuItem::linkToExitImpersonation(t('Stop impersonation'), 'fas fa-door-open'),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Rules'), 'fa fa-file', Rules::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Rules::class)->setAction(Crud::PAGE_NEW),
            ]);

            yield MenuItem::section(t('Tickets Settings'));
            yield MenuItem::subMenu(t('Support'), 'fas fa-ticket')->setSubItems([
                MenuItem::linkToCrud(t('Ticket'), 'fas fa-ticket', Ticket::class),
                MenuItem::linkToCrud(t('Response'), 'fab fa-weixin', TicketsResponse::class),
                MenuItem::linkToCrud(t('Level'), 'fas fa-chart-simple', Level::class),
                MenuItem::linkToCrud(t('Status'), 'fas fa-check', Status::class),
            ]);
        }

        if ($this->isGranted(HasRoles::EDITOR)) {
            yield MenuItem::section(t('Blog Settings'));
            yield MenuItem::subMenu(t('Post Settings'), 'fas fa-newspaper')->setSubItems([
                MenuItem::linkToCrud(t('Post'), 'fas fa-newspaper', Post::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', Post::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Category'), 'fab fa-delicious', PostCategory::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', PostCategory::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud(t('Type'), 'fas fa-bars', PostType::class),
                MenuItem::linkToCrud(t('Add'), 'fas fa-plus', PostType::class)->setAction(Crud::PAGE_NEW),
            ]);
        }

        if ($this->isGranted(HasRoles::ADMINAPPLICATION)) {
            yield MenuItem::section(t('Comment Settings'));
            yield MenuItem::linkToCrud(t('Comments'), 'fas fa-comment', Comment::class);
        }

        yield MenuItem::section();
        yield MenuItem::linkToLogout(t('Sign Out'), 'fa fa-sign-out');
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->renderContentMaximized()
            ->showEntityActionsInlined()
            ->setDefaultSort([
                'id' => 'DESC',
            ])
        ;
    }

    public function configureUserMenu(UserInterface|User $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUsername())
            ->displayUserName(false)
            ->addMenuItems([
                MenuItem::linkToRoute(t('My Profile'), 'fa fa-id-card', 'dashboard_account_profile'),
                MenuItem::linkToRoute(t('Settings'), 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section(),
                MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ])
        ;
    }
}
