<?php

namespace App\Controller\Shop;

use App\Form\Shop\SearchEngineFormType;
use App\Repository\Shop\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchEngineController extends AbstractController
{
    #[Route(path: '/search-engine', name: 'search_engine', methods: ['GET'])]
    public function searchEngine(Request $request, ProductRepository $productRepository): Response
    {
        $searchForm = $this->createForm(SearchEngineFormType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        $searchKeyword = $request->getPayload()->get('keyword');

        $results = [];

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchKeyword = $searchForm['keyword']->getData();
            $results = $productRepository->searchEngine($searchKeyword);
        }

        return $this->render('shop/search-engine.html.twig', [
            'searchForm' => $searchForm,
            'searchKeyword' => $searchKeyword,
            'results' => $results,
        ]);
    }

    /*
    public function searchEngine(Request $request, ProductRepository $productRepository): Response
    {
        $results = [];

        if ($request->isMethod('GET')) {
            //$data = $request->request->all();
            $data = $request->query->all();
            $word = $data['word'];
            $results = $productRepository->searchEngine($word);
        }

        return $this->render('shop/search-engine.html.twig', [
            'products' => $results,
            'word' => $word
        ]);
    }
    */

    /*
    #[Route(path: '/search-engine', name: 'search_engine', methods: ['GET'])]
    public function searchEngine(Request $request): Response
    {
        return $this->render('shop/search-engine/search-engine.html.twig', [
            'query' => (string) $request->query->get('q', ''),
        ]);
    }
    */
}
