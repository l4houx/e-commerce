<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/home.html.twig', [
            'products' => $productRepository->findRecent(6),
            //'categories' => $categoryRepository->findAll(),
        ]);
    }
}
