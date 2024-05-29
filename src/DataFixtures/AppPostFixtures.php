<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Service\AvatarService;
use App\Entity\Traits\HasRoles;
use App\DataFixtures\FakerTrait;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppPostFixtures extends Fixture implements DependentFixtureInterface
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
        // User Admin
        /** @var User $author */
        $authors = [];
        $author = (new User());
        $avatar = $this->avatarService->createAvatar($author->getEmail());
        $author
            ->setId(4)
            ->setAvatar($author)
            //->setTeamName('author.jpg')
            ->setRoles([HasRoles::ADMIN])
            ->setLastname('Tom')
            ->setFirstname('Doe')
            ->setUsername('tom-admin')
            //->setSlug('tom-admin')
            ->setEmail('tom-admin@yourdomain.com')
            //->setPhone($this->faker()->phoneNumber())
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
            $author->setPassword(
                $this->hasher->hashPassword($author, 'author')
            )
        );
        $authors[] = $author;

        $tags = [
            'Creators', 'Branding', 'Budgeting', 'Catering', 'Collaboration', 
            'Community', 'Content', 'Feature', 'News', 'Pricing', 'Marketing',
            'Social Media', 'Sponsoring', 'Tips', 'Planning',
        ];

        // Create 20 Posts
        $posts = [];
        for ($i = 0; $i <= 20; ++$i) {
            $post = new Post();
            $post
                ->setName($this->faker()->unique()->sentence(5, true))
                ->setSlug($this->slugger->slug($post->getName())->lower())
                ->setContent($this->faker()->paragraphs(10, true))
                ->setReadtime(rand(10, 160))
                ->setAuthor($author)
                //->setTags(mt_rand(0, 1) === 1 ? $this->faker()->unique()->word() : null)
                //->setTags(mt_rand(0, 1) === 1 ? $tags : null)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
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

            $category = $this->getReference('category-' . $this->faker()->numberBetween(1, 16));
            $post->setCategory($category);

            $type = $this->getReference('type-'.$this->faker()->numberBetween(1, 4));
            $post->setType($type);

            shuffle($tags);
            foreach (array_slice($tags, 0, 2) as $tag) {
                $post->setTags(mt_rand(0, 1) === 1 ? $tag : null);
            }

            $manager->persist($post);
            $posts[] = $post;

            // Create Comments
            for ($k = 1; $k <= $this->faker()->numberBetween(1, 5); ++$k) {
                $comment = (new Comment())
                    ->setIp($this->faker()->ipv4)
                    ->setContent($this->faker()->paragraph())
                    ->setAuthor($this->getReference('user-' . $this->faker()->numberBetween(1, 10)))
                    ->setPost($post)
                    ->setParent(null)
                    ->setIsApproved($this->faker()->numberBetween(0, 1))
                    ->setIsRGPD(true)
                    ->setPublishedAt(\DateTimeImmutable::createFromMutable($this->faker()->dateTime()))
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
            AppPostTypeFixtures::class
        ];
    }
}
