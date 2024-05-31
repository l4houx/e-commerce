<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Product::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->orderBy('p.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('p.updatedAt <= :now')
            ->orWhere('p.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::PRODUCT_LIMIT,
            ['wrap-queries' => true],
            [
                'distinct' => false,
                'sortFieldAllowList' => ['p.id', 'p.name'],
            ]
        );
    }

    public function findForUserPagination(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')->leftJoin('p.subCategories', 'c')->select('p', 's');

        if ($userId) {
            $builder = $builder->andWhere('p.author = :user')->setParameter('user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::PRODUCT_LIMIT,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['p.id', 'p.name', 'p.subCategories'],
            ]
        );
    }

    public function getForPagination(
        int $page,
        int $limit,
        string $sort,
        ?SubCategory $subCategory,
        Filter $filter
    ): PaginationInterface {
        $builder = $this->createQueryBuilder("p")
            ->addSelect("b")
            ->addSelect("s")
            ->join("p.brand", "b")
            ->join("p.subCategories", "s")
            ->andWhere('p.isOnline = true')
            ->andWhere("p.price >= :min")
            ->setParameter("min", $filter->min)
            ->andWhere("p.price <= :max")
            ->setParameter("max", $filter->max)
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
        ;

        $this->filterProducts($builder, $subCategory, $filter);

        switch ($sort) {
            case "price-asc":
                $builder->orderBy("p.price", "asc");
                break;
            case "price-desc":
                $builder->orderBy("p.price", "desc");
                break;
            case "name-asc":
                $builder->orderBy("p.name", "asc");
                break;
            case "name-desc":
                $builder->orderBy("p.name", "desc");
                break;
            default:
                $builder->orderBy("s.id", "desc")->orderBy("p.id", "desc");
                break;
        }

        $products = $this->paginator->paginate($builder, $page);

        return $products;
    }

    private function filterProducts(
        QueryBuilder $builder,
        ?SubCategory $subCategory,
        Filter $filter
    ): void {
        if ($filter->brand !== null) {
            $builder
                ->andWhere("b = :brand")
                ->setParameter("brand", $filter->brand)
            ;
        }

        if ($filter->keywords !== null) {
            $builder
                ->andWhere("CONCAT(p.name, ' ', p.content, ' ', b.name) LIKE :keywords")
                ->setParameter("keywords", "%" . $filter->keywords . "%")
            ;
        }
    }

    public function getMinPrice(): int
    {
        return $this->createQueryBuilder("p")
            ->select("p.price")
            ->where("p.isOnline = true")
            ->orderBy("p.price", "asc")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getMaxPrice(): int
    {
        return $this->createQueryBuilder("p")
            ->select("p.price")
            ->where("p.isOnline = true")
            ->orderBy("p.price", "desc")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findByKeyword(string $keyword): array
    {
        $qb = $this
            ->createQueryBuilder('p');

        $qb
            ->select('p.id', 'p.content')
            ->where($qb->expr()->like('p.id', ':keyword'))
            ->orWhere($qb->expr()->like('p.content', ':keyword'))
            ->setParameter('keyword', '%'.$keyword .'%');

        return $qb
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * @return QueryBuilder<Product>
     */
    public function findRecents(int $maxResults): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.online = true AND p.createdAt < NOW()')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($maxResults)
        ;
    }

    public function findRecent(int $maxResults): array
    {
        return $this->createQueryBuilder('p')
            //->andWhere('p.isOnline = :isOnline')
            ->andWhere('p.createdAt <= :now')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Product[] returns an array of Product objects similar with the given product
     */
    public function findSimilar(Product $product, int $maxResults = 4): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.subCategories', 's')
            ->addSelect('COUNT(s) AS HIDDEN numberOfSubCategories')
            ->andWhere('s IN (:subCategories)')
            ->andWhere('p != :product')
            ->setParameter('subCategories', $product->getSubCategories())
            ->setParameter('product', $product)
            ->groupBy('p')
            ->addOrderBy('numberOfSubCategories', 'DESC')
            ->addOrderBy('p.createdAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getProducts($id, $sort, $order, $limit, $count): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if ($count) {
            $qb->select('COUNT(p)');
        } else {
            $qb->select('p');
        }

        if ('all' !== $id) {
            $qb->andWhere('p.id = :id')->setParameter('id', $id);
        }

        $qb->orderBy($sort, $order);
        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }
}
