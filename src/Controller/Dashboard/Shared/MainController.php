<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/** My Profile User */
#[IsGranted(HasRoles::DEFAULT)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/user', name: 'dashboard_user_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('%website_dashboard_path%/shared/index.html.twig');
    }
}
