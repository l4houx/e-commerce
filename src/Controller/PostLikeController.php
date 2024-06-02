<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Traits\HasRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostLikeController extends BaseController
{
    #[Route(path: '/post/like/{id}', name: 'post_like', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(HasRoles::DEFAULT)]
    public function postLike(Post $post, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $user = $this->getUserOrThrow();

        if ($post->isLikedByUser($user)) {
            $post->removeLike($user);
            $em->flush();

            return $this->json([
                'message' => $translator->trans('The like was deleted successfully.'),
                'nbLike' => $post->howManyLikes()
            ]);
        }

        $post->addLike($user);
        $em->flush();

        return $this->json([
            'message' => $translator->trans('The like was added successfully.'),
            'nbLike' => $post->howManyLikes()
        ]);
    }
}
