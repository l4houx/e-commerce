<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\RolesField;
use App\Controller\Admin\Field\RulesAgreementField;
use App\Controller\Admin\Traits\CreateEditTrait;
use App\Entity\Traits\HasRoles;
use App\Entity\User\Manager;
use App\Service\AvatarService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

use function Symfony\Component\Translation\t;

class ManagerCrudController extends AbstractCrudController
{
    use CreateEditTrait;

    public function __construct(
        private readonly AvatarService $avatarService
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Manager::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel(t('Avatar'))->hideOnForm();
        yield TextField::new('avatar', t('Avatar'))->hideOnForm();

        yield FormField::addPanel(t('Administrator'));
        yield ChoiceField::new('civility')->setChoices([
            'Sir' => 'Mr',
            'Madam' => 'Mme',
            'Miss' => 'Mlle',
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
            ->setTemplatePath('admin/field/user_rules_agreements.html.twig')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('member', t('Member (main)'))
            ->setCrudController(MemberCrudController::class)
        ;
        yield AssociationField::new('members', t('Members'))
            ->setCrudController(MemberCrudController::class)
            ->setTemplatePath('admin/field/manager_members.html.twig')
            ->hideOnIndex()
        ;

        yield FormField::addPanel(t('Date'))->hideOnForm();
        yield DateTimeField::new('createdAt', t('Creation date'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('updatedAt', t('Last modification'))->hideOnForm()->onlyOnDetail();
        yield DateTimeField::new('deletedAt', t('Deleted At'))->hideOnForm()->hideOnIndex();
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
