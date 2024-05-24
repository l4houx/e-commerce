<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(ProductRepository $productRepository): Response
    {
        return $this->render('home/home.html.twig', [
            'rows' => $productRepository->findAll(),
        ]);
    }
}
