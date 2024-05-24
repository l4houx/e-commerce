<?php

namespace App\DataFixtures;

use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Service\AvatarService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppAdminTeamUserFixtures extends Fixture
{
    use FakerTrait;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly AvatarService $avatarService,
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // User Super Admin Application
        /** @var User $superadmin */
        $avatar = $this->avatarService->createAvatar($superadmin->getEmail());
        $superadmin = (new User());
        $superadmin
            ->setId(1)
            ->setAvatar($avatar)
            // ->setTeamName('superadmin.jpg')
            ->setRoles([HasRoles::ADMINAPPLICATION])
            ->setLastname('Cameron')
            ->setFirstname('Williamson')
            ->setUsername('superadmin')
            // ->setSlug('superadmin')
            ->setEmail('superadmin@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Super Admin Staff')
            ->setLastLogin(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
        ;

        $manager->persist(
            $superadmin->setPassword(
                $this->hasher->hashPassword($superadmin, 'superadmin')
            )
        );

        // User Admin
        /** @var User $admin */
        $avatar = $this->avatarService->createAvatar($admin->getEmail());
        $admin = (new User());
        $admin
            ->setId(2)
            ->setAvatar($avatar)
            // ->setTeamName('admin.jpg')
            ->setRoles([HasRoles::ADMIN])
            ->setLastname('Wade')
            ->setFirstname('Warren')
            ->setUsername('admin')
            // ->setSlug('admin')
            ->setEmail('admin@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Admin Staff')
            ->setLastLogin(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
        ;

        $manager->persist(
            $admin->setPassword(
                $this->hasher->hashPassword($admin, 'admin')
            )
        );

        // User Moderator
        /** @var User $moderator */
        $avatar = $this->avatarService->createAvatar($moderator->getEmail());
        $moderator = (new User());
        $moderator
            ->setId(3)
            ->setAvatar($moderator)
            // ->setTeamName('moderator.jpg')
            ->setRoles([HasRoles::MODERATOR])
            ->setLastname('Jane')
            ->setFirstname('Cooper')
            ->setUsername('moderator')
            // ->setSlug('moderator')
            ->setEmail('moderator@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Moderator Staff')
            ->setLastLogin(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
        ;

        $manager->persist(
            $moderator->setPassword(
                $this->hasher->hashPassword($moderator, 'moderator')
            )
        );

        // User Editor
        /** @var User $editor */
        $avatar = $this->avatarService->createAvatar($editor->getEmail());
        $editor = (new User());
        $editor
            ->setId(4)
            ->setAvatar($editor)
            // ->setTeamName('editor.jpg')
            ->setRoles([HasRoles::EDITOR])
            ->setLastname('Roberto')
            ->setFirstname('Cooper')
            ->setUsername('editor')
            // ->setSlug('editor')
            ->setEmail('editor@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Editor Staff')
            ->setLastLogin(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
        ;

        $manager->persist(
            $editor->setPassword(
                $this->hasher->hashPassword($editor, 'editor')
            )
        );

        // Create 10 Users
        $genres = ['male', 'female'];
        $genre = $this->faker()->randomElement($genres);
        for ($i = 0; $i <= 10; ++$i) {
            /** @var User $user */
            $avatar = $this->avatarService->createAvatar($user->getEmail());
            $user = (new User());
            $user
                ->setAvatar($avatar)
                ->setLastname($this->faker()->lastName)
                ->setFirstname($this->faker()->firstName($genre))
                ->setUsername($this->faker()->unique()->userName())
                // ->setSlug($this->slugger->slug($user->getUsername())->lower())
                ->setEmail($this->faker()->email())
                ->setLastLogin(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setLastLoginIp($this->faker()->ipv4())
                // ->setPhone($this->faker()->phoneNumber())
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
            ;

            if ($i > 5) {
                $user->setIsVerified(false);
                $user->setIsSuspended($this->faker()->numberBetween(0, 1));
                $user->setIsAgreeTerms($this->faker()->numberBetween(0, 1));
            } else {
                $user->setIsVerified(true);
            }

            $this->addReference('user-'.$i, $user);

            $manager->persist(
                $user->setPassword(
                    $this->hasher->hashPassword($user, 'user')
                )
            );
        }

        $manager->flush();
    }
}
