<?php

namespace App\Service;

use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use App\Event\PreProductCreatedEvent;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Psr\EventDispatcher\EventDispatcherInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EventDispatcherInterface $dispatcher, 
        private readonly EntityManagerInterface $em,
        private readonly Security $security
    ) {
    }

    public function findOneById(string $id): ?Product
    {
        return $this->productRepository->findOneBy(['id' => $id]);
    }

    public function findByKeyword(string $keyword): array
    {
        return $this->productRepository->findByKeyword($keyword);
    }

    public function createProduct(Product $product): void
    {
        $product->setCreatedAt(new \DateTimeImmutable());
        $product->setUpdatedAt(new \DateTimeImmutable());
        $this->dispatcher->dispatch(new PreProductCreatedEvent($product));
        $this->em->persist($product);
        $this->em->flush();
        $this->dispatcher->dispatch(new ProductCreatedEvent($product));
    }

    public function updateProduct(Product $product): void
    {
        $product->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }
}
