<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
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

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
