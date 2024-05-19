<?php

namespace App\Controller\Dashboard\Shared;

use App\Entity\User;
use App\Entity\Traits\HasRoles;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/** My Profile User */
#[IsGranted(HasRoles::DEFAULT)]
class MainController extends BaseController
{
    #[Route(path: '/%website_dashboard_path%/user', name: 'dashboard_user_index', methods: ['GET'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('dashboard/shared/index.html.twig', compact('user'));
    }
}
