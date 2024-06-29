<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Size;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Size>
 */
class SizeRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Size::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('s')
            ->orderBy('s.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('s.updatedAt <= :now')
            ->orWhere('s.isActive = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::SIZE_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['s.id', 's.name'],
            ]
        );
    }
}
