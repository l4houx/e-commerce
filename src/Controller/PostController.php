<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $em,
        private readonly CommentRepository $commentRepository,
        private readonly PostRepository $postRepository,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '/posts/{category}', name: 'posts', methods: ['GET'])]
    public function posts(Request $request, PaginatorInterface $paginator, string $category = 'all'): Response
    {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');
        $posts = $paginator->paginate($this->settingService->getBlogPosts(['category' => $category, 'keyword' => $keyword])->getQuery(), $request->query->getInt('page', 1), $this->settingService->getSettings('posts_per_page'), ['wrap-queries' => true]);

        return $this->render('post/posts.html.twig', compact('posts'));
    }

    #[Route(path: '/post/{slug}', name: 'post', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['GET'])]
    public function post(Request $request, string $slug): Response
    {
        /** @var Post $post */
        $post = $this->settingService->getBlogPosts(['slug' => $slug])->getQuery()->getOneOrNullResult();
        if (!$post) {
            $this->addFlash('danger', $this->translator->trans('The article not be found'));

            return $this->redirectToRoute('posts', [], Response::HTTP_SEE_OTHER);
        }

        $page = $request->query->getInt('page', 1);
        //$comments = $this->commentRepository->findForPagination($page);
        $comments = $this->commentRepository->findForPaginationValue($post, $page);

        $previous = $this->postRepository->findPrevious($post);
        $next = $this->postRepository->findNext($post);

        $post->viewed();

        $this->em->persist($post);
        $this->em->flush();

        return $this->render('post/post.html.twig', compact('comments', 'post', 'previous', 'next'));
    }
}
