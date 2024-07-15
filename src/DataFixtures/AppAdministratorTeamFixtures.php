<?php

namespace App\DataFixtures;

use App\Entity\SuperAdministrator;
use App\Entity\Traits\HasRoles;
use App\Service\AvatarService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppAdministratorTeamFixtures extends Fixture implements FixtureGroupInterface
{
    use FakerTrait;

    private int $autoIncrement;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly AvatarService $avatarService,
        private readonly SluggerInterface $slugger
    ) {
        $this->autoIncrement = 1;
    }

    public function load(ObjectManager $manager): void
    {
        // SuperAdministrator Application
        /** @var SuperAdministrator $superadmin */
        $superadmin = (new SuperAdministrator());
        $superadmin
            ->setId(1)
            // ->setAvatar($this->avatarService->createAvatar($superadmin->getEmail()))
            // ->setTeamName('superadmin.jpg')
            ->setRoles([HasRoles::ADMINAPPLICATION])
            ->setCivility('Mr')
            ->setLastname('Cameron')
            ->setFirstname('Williamson')
            ->setUsername('superadmin')
            // ->setSlug('superadmin')
            ->setEmail('superadmin@yourdomain.com')
            ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsAgreeTerms(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Super Admin Staff')
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $this->setReference('SuperAdministrator', $superadmin);

        $manager->persist(
            $superadmin->setPassword(
                $this->hasher->hashPassword($superadmin, 'superadmin')
            )
        );

        // SuperAdministrator Admin
        /** @var SuperAdministrator $admin */
        $admin = (new SuperAdministrator());
        $admin
            ->setId(2)
            // ->setAvatar($this->avatarService->createAvatar($admin->getEmail()))
            // ->setTeamName('admin.jpg')
            ->setRoles([HasRoles::ADMIN])
            ->setCivility('Mr')
            ->setLastname('Wade')
            ->setFirstname('Warren')
            ->setUsername('admin')
            // ->setSlug('admin')
            ->setEmail('admin@yourdomain.com')
            ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsAgreeTerms(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Admin Staff')
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $this->setReference('Admin', $admin);

        $manager->persist(
            $admin->setPassword(
                $this->hasher->hashPassword($admin, 'admin')
            )
        );

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['prod'];
    }
}
