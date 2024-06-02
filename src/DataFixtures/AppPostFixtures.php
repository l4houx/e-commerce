<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppPostFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        // User Admin
        /** @var User $author */
        $authors = [];
        $author = (new User());
        // $avatar = $this->avatarService->createAvatar($author->getEmail());
        $author
            ->setId(5)
            // ->setAvatar($author)
            // ->setTeamName('author.jpg')
            ->setRoles([HasRoles::ADMIN])
            ->setLastname('Tom')
            ->setFirstname('Doe')
            ->setUsername('tom-admin')
            // ->setSlug('tom-admin')
            ->setEmail('tom-admin@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
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

        $manager->persist(
            $author->setPassword(
                $this->hasher->hashPassword($author, 'author')
            )
        );
        $authors[] = $author;

        $tags = ['France', 'Politics', 'World', 'Computer Science', 'Economy', 'Associations'];

        // Create 20 Posts
        $posts = [];
        for ($i = 0; $i <= 20; ++$i) {
            $post = new Post();
            $post
                ->setName($this->faker()->unique()->sentence(5, true))
                ->setSlug($this->slugger->slug($post->getName())->lower())
                ->setContent($this->faker()->paragraphs(10, true))
                ->setReadtime(rand(10, 160))
                // ->setAuthor($author)
                // ->setTags(mt_rand(0, 1) === 1 ? $this->faker()->unique()->word() : null)
                // ->setTags(mt_rand(0, 1) === 1 ? $tags : null)
                ->setMetaTitle($post->getName())
                ->setMetaDescription($this->faker()->realText(100))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            if ($i > 10) {
                $post->setIsOnline(false);
            } else {
                $post->setIsOnline(true);
                $post->setViews(rand(10, 160));
                $post->setPublishedAt(
                    $this->faker()->boolean(75)
                    ? \DateTime::createFromInterface(
                        $this->faker()->dateTimeBetween('-50 days', '+10 days')
                    )
                    : null
                );
            }

            $category = $this->getReference('category-'.$this->faker()->numberBetween(1, 6));
            $post->setCategory($category);

            $type = $this->getReference('type-'.$this->faker()->numberBetween(1, 4));
            $post->setType($type);

            shuffle($tags);
            foreach (array_slice($tags, 0, 2) as $tag) {
                $post->setTags(1 === mt_rand(0, 1) ? $tag : null);
            }

            // Create Post Like
            for ($i = 0; $i < mt_rand(0, 15); $i++) {
                $post->addLike(
                    $users[mt_rand(0, count($users) - 1)]
                );
            }

            $manager->persist($post);
            $posts[] = $post;

            // Create Comments
            for ($k = 1; $k <= $this->faker()->numberBetween(1, 5); ++$k) {
                $comment = (new Comment())
                    ->setIp($this->faker()->ipv4)
                    ->setContent($this->faker()->paragraph())
                    ->setAuthor($this->getReference('user-'.$this->faker()->numberBetween(1, 10)))
                    ->setPost($post)
                    ->setParent(null)
                    ->setIsApproved($this->faker()->numberBetween(0, 1))
                    ->setIsRGPD(true)
                    ->setPublishedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ;

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppAdminTeamUserFixtures::class,
            AppPostCategoryFixtures::class,
            AppPostTypeFixtures::class,
        ];
    }
}
