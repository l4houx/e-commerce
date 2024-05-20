<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/page')]
class PagesController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/access-denied', name: 'access_denied', methods: ['GET'])]
    public function accessDenied(): Response
    {
        return $this->render('pages/access-denied.html.twig');
    }
}
