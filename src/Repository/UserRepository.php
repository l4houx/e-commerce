<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, User::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('u')
            ->orderBy('u.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('u.updatedAt <= :now')
            ->orWhere('u.isVerified = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::USER_LIMIT,
            ['wrap-queries' => true],
            [
                'distinct' => false,
                'sortFieldAllowList' => ['u.id', 'u.username', 'u.lastname', 'u.firstname'],
            ]
        );
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

        /**
     * Query used to retrieve a user for the login.
     */
    public function findUserByEmailOrUsername(string $usernameOrEmail): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.email) = :identifier')
            // ->where('LOWER(u.email) = :identifier OR LOWER(u.username) = :identifier')
            // ->andWhere('u.isVerified = true')
            ->orWhere('LOWER(u.username) = :identifier')
            ->setParameter('identifier', mb_strtolower($usernameOrEmail))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return User[]
     */
    public function findUsers()
    {
        return $this->createQueryBuilder('u')
            ->where('u.isVerified = all')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
