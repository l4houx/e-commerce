<?php

namespace App\Controller;

use App\Entity\Page;
use App\Service\SettingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/page')]
class PagesController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'pages', methods: ['GET'])]
    public function pages(): Response
    {
        return $this->render('pages/pages.html.twig');
    }

    #[Route(path: '/{slug}', name: 'page', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function page(Request $request, string $slug): Response
    {
        $page = $this->settingService->getPages(['slug' => $slug])->getQuery()->getOneOrNullResult();

        if (!$page) {
            $this->addFlash('danger', $this->translator->trans('The page can not be found'));

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/page-detail.html.twig', compact('page'));
    }

    #[Route(path: '/access-denied', name: 'access_denied', methods: ['GET'])]
    public function accessDenied(): Response
    {
        return $this->render('pages/access-denied.html.twig');
    }
}
