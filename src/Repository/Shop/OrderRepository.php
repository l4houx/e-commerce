<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Order;
use App\Entity\Traits\HasLimit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Order::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('o')
            ->addSelect('od')
            ->leftJoin('o.orderDetails', 'od')
            ->orderBy('o.id', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('o.createdAt <= :now')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::ORDER_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['o.id', 'o.firstname', 'o.lastname', 'o.ref'],
            ]
        );
    }

    public function getOrderDeliveredPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('o')
            ->addSelect('od')
            ->leftJoin('o.orderDetails', 'od')
            ->orderBy('o.id', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('o.createdAt <= :now')
            ->orWhere('o.isCompleted = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::ORDER_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['o.id', 'o.firstname', 'o.lastname', 'o.ref'],
            ]
        );
    }

    public function getOrders($status, $ref, /* $user, */ $orderDetails, $sort, $order, $limit, $count): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');

        if ($count) {
            $qb->select('COUNT(o)');
        } else {
            $qb->select('o');
        }

        if ('all' !== $status) {
            $qb->andWhere('o.status = :status')->setParameter('status', $status);
        }

        /*
        if ($user !== "all") {
            $qb->leftJoin("o.user", "user");
            $qb->andWhere("user.slug = :user")->setParameter("user", $user);
        }
        */

        if ('all' !== $orderDetails) {
            $qb->leftJoin('o.orderDetails', 'orderDetails');
        }

        if ('all' !== $ref) {
            $qb->andWhere('o.ref = :ref')->setParameter('ref', $ref);
        }

        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        if ($sort) {
            $qb->orderBy('o.'.$sort, $order);
        }

        return $qb;
    }
}
