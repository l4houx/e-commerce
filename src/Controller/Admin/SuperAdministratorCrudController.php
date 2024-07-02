<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\AvatarService;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasRoles;
use App\Entity\SuperAdministrator;
use App\Controller\Admin\Field\RolesField;
use function Symfony\Component\Translation\t;
use App\Controller\Admin\Traits\CreateEditTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use App\Controller\Admin\Field\RulesAgreementField;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\NotNull;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Symfony\Component\Validator\Constraints\NotBlank;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;

use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @method User getUser()
 */
class SuperAdministratorCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public static function getEntityFqcn(): string
    {
        return SuperAdministrator::class;
    }

    public function __construct(
        private readonly EntityRepository $entityRepo,
        private readonly AvatarService $avatarService,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Avatar'))->hideOnForm();
        yield TextField::new('avatar', t('Avatar'))->hideOnForm();

        yield FormField::addPanel(t('Administrator'));
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
        yield TextField::new('phone', t('Phone Number'))
            ->setFormTypeOption('constraints', [
                new NotBlank(),
                new Regex(['pattern' => '/^0[0-9]{9}$/']),
            ])->hideOnIndex()
        ;

        yield RolesField::new('role', 'Role')
            ->hideOnForm()
        ;

        yield FormField::addPanel(t('Team'));
        yield BooleanField::new('isTeam', t('Team'))->hideOnIndex();
        yield TextareaField::new('about', t('About'))->hideOnIndex();
        yield TextareaField::new('designation', t('Designation'))->hideOnIndex();

        yield FormField::addPanel(t('Details'));
        yield BooleanField::new('isVerified', t('Verified'));
        yield BooleanField::new('isAgreeTerms', t('Agree terms'))->hideOnIndex();
        yield BooleanField::new('isSuspended', t('Suspended'))->hideOnIndex();
        yield DateTimeField::new('bannedAt', t('Banner'))->hideOnForm();
        yield DateTimeField::new('lastLogin', t('Last connection'))->hideOnForm();
        yield TextField::new('lastLoginIp', t('IP'))->hideOnForm();

        yield RulesAgreementField::new('lastRulesAgreement', t('Rule'))->hideOnForm();
        yield AssociationField::new('rulesAgreements', t('Rule'))
            ->setTemplatePath("admin/field/user_rules_agreements.html.twig")
            ->onlyOnDetail()
        ;

        yield AssociationField::new('member', t('Member (main)'))
            ->setCrudController(MemberCrudController::class)
        ;
        /*yield AssociationField::new('members', t('Members'))
            ->setCrudController(MemberCrudController::class)
            ->setTemplatePath('admin/field/manager_members.html.twig')
            ->hideOnIndex()
        ;*/

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deleted At'))->hideOnForm()->hideOnIndex();
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
            ->setEntityLabelInSingular(t('Administrator'))
            ->setEntityLabelInPlural(t('Administrators'))
            ->setDefaultSort(['firstname' => 'ASC', 'lastname' => 'ASC'])
        ;
    }
}