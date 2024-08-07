<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\Shop\ReviewRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class UserController extends BaseController
{
    #[Route(path: '/profile/{id}', name: 'user_profil', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function userProfil(
        User $user,
        CommentRepository $commentRepository,
        ReviewRepository $reviewRepository
    ): Response {
        return $this->render('user/profile-manager.html.twig', [
            'user' => $user,
            'lastComments' => $commentRepository->getLastByUser($user, 4),
            'lastReviews' => $reviewRepository->getLastByUser($user, 4),
        ]);
    }
}
