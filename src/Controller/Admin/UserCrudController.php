<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\AvatarService;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasRoles;
use Symfony\Component\Mime\Address;
use App\Service\GeneratorTokenService;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\Field\RolesField;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use function Symfony\Component\Translation\t;
use Symfony\Component\Mailer\MailerInterface;
use App\Controller\Admin\Traits\ReadOnlyTrait;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Validator\Constraints\Email;
use App\Controller\Admin\Field\RulesAgreementField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotNull;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Validator\Constraints\NotBlank;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @method User getUser()
 */
class UserCrudController extends AbstractCrudController
{
    use ReadOnlyTrait;

    public function __construct(
        private readonly EntityRepository $entityRepo,
        private readonly AvatarService $avatarService,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $userId = $this->getUser()->getId();

        $response = $this->entityRepo->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->andWhere('entity.id != :userId')->setParameter('userId', $userId);

        return $response;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission(HasRoles::ADMINAPPLICATION)
            ->setEntityLabelInSingular(t('User'))
            ->setEntityLabelInPlural(t('Users'))
            ->setDefaultSort(['firstname' => 'ASC', 'lastname' => 'ASC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $logAs = Action::new('logAs', t('Login as'))
            ->displayAsLink()
            ->linkToRoute('home', fn (User $user) => ['_switch_user' => $user->getEmail()])
        ;

        $reset = Action::new('reset', t('Reset'))
            ->displayAsLink()
            ->linkToRoute('admin_user_reset', fn (User $user) => ['user' => $user->getId()])
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $logAs)
            ->add(Crud::PAGE_DETAIL, $logAs)
            ->add(Crud::PAGE_INDEX, $reset)
            ->add(Crud::PAGE_DETAIL, $reset)

            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Avatar'))->hideOnForm();
        yield TextField::new('avatar', t('Avatar'))->hideOnForm();

        yield FormField::addPanel(t('User'));
        yield ChoiceField::new('civility')->setChoices([
            'Sir' => 'Mr', 
            'Madam' => 'Mme', 
            'Miss' => 'Mlle'
        ]);
        yield TextField::new('firstname', t('Firstname'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 2, max: 20),
            ])
        ;
        yield TextField::new('lastname', t('Lastname'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 2, max: 20),
            ])
        ;
        yield TextField::new('username', t('Username'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Length(min: 4, max: 30),
            ])
        ;
        yield TextField::new('password', t('Password'))
            ->setFormType(PasswordType::class)
            ->setFormTypeOption('constraints', [
                new NotBlank(),
            ])
            ->onlyWhenCreating()
        ;
        yield EmailField::new('email', t('Email'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new NotNull(),
                new Email(),
                new Length(min: 5, max: 180),
            ])
        ;
        yield RolesField::new('role', 'Role')
            ->hideOnForm()
        ;

        yield FormField::addPanel(t('Team'));
        yield BooleanField::new('isTeam', t('Team'));
        yield TextareaField::new('about', t('About'))->hideOnIndex();
        yield TextareaField::new('designation', t('Designation'))->hideOnIndex();

        yield FormField::addPanel(t('Details'));
        yield BooleanField::new('isVerified', t('Verified'));
        yield BooleanField::new('isAgreeTerms', t('Agree terms'))->renderAsSwitch(false)->hideOnIndex();
        yield BooleanField::new('isSuspended', t('Suspended'));
        yield DateTimeField::new('bannedAt', t('Banner'))->hideOnIndex()->hideOnForm();
        yield DateTimeField::new('lastLogin', t('Last connection'))->hideOnIndex()->hideOnForm();
        yield TextField::new('lastLoginIp', t('IP'))->hideOnIndex()->hideOnForm();

        yield RulesAgreementField::new('lastRulesAgreement', t('Rule'))->hideOnForm();
        yield AssociationField::new('rulesAgreements', t('Rule'))
            ->setTemplatePath("admin/field/user_rules_agreements.html.twig")
            ->onlyOnDetail()
        ;

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deleted At'))->hideOnForm()->hideOnIndex();
    }

    #[Route(path: '/%website_admin_path%/users/{id}/reset', name: 'admin_user_reset', methods: ['GET'])]
    public function reset(
        User $user,
        TranslatorInterface $translator,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        GeneratorTokenService $generatorTokenService,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        $password = $generatorTokenService->generateToken();
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $em->flush();

        $mailer->send(
            (new TemplatedEmail())
                ->from(new Address(
                    $this->getParameter('website_no_reply_email'),
                    $this->getParameter('website_name'),
                ))
                ->to(new Address($user->getEmail(), $user->getFullName()))
                ->htmlTemplate('mails/reset.html.twig')
                ->context(['customer' => $user, 'password' => $password])
        );

        $this->addFlash(
            'success',
            sprintf(
                $translator->trans('A new password has been generated and sent to %s.'),
                $user->getFullName()
            )
        );

        return $this->redirect(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }
}
