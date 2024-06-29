<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Color;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Color>
 */
class ColorRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Color::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('c')
            ->orderBy('c.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('c.updatedAt <= :now')
            ->orWhere('c.isActive = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::COLOR_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['c.id', 'c.name'],
            ]
        );
    }
}
