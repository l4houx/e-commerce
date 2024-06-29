<?php

namespace App\Repository;

use App\Entity\SuperAdministrator;
use App\Entity\SuperSuperAdministrator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<SuperAdministrator>
 */
class SuperAdministratorRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuperAdministrator::class);
    }

    /**
     * Used to upgrade (rehash) the superadministrator's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $superadministrator, string $newHashedPassword): void
    {
        if (!$superadministrator instanceof SuperAdministrator) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $superadministrator::class));
        }

        $superadministrator->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($superadministrator);
        $this->getEntityManager()->flush();
    }

    /**
     * Query used to retrieve a user superadministrator for the login.
     */
    public function findUserByEmailOrUsername(string $usernameOrEmail): ?SuperAdministrator
    {
        return $this->createQueryBuilder('a')
            ->where('LOWER(a.email) = :identifier')
            // ->where('LOWER(a.email) = :identifier OR LOWER(a.username) = :identifier')
            // ->andWhere('a.isVerified = true')
            ->orWhere('LOWER(a.username) = :identifier')
            ->setParameter('identifier', mb_strtolower($usernameOrEmail))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return SuperAdministrator[] Returns an array of SuperAdministrator objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SuperAdministrator
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
