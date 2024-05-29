<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Comment;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Service\SettingService;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route(path: '/%website_dashboard_path%/admin/manage-comments', name: 'dashboard_admin_comment_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class CommentController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly CommentRepository $commentRepository,
        private readonly EntityManagerInterface $em,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $rows = $this->commentRepository->findForPagination($page);

        return $this->render('dashboard/admin/comment/index.html.twig', compact('rows'));
    }

    #[Route(path: '/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('comment_deletion_'.$comment->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($comment);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $comment->getAuthor()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/show', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[Route(path: '/{id}/hide', name: 'hide', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function showhide(Comment $comment, int $id): Response
    {
        /** @var Comment $comment */
        $comment = $this->settingService->getComments(['isApproved' => 'all', 'id' => $id])->getQuery()->getOneOrNullResult();

        if (!$comment) {
            $this->addFlash('danger', $this->translator->trans('The comment can not be found'));

            return $this->redirectToRoute('dashboard_admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        if (false === $comment->getIsApproved()) {
            $comment->setIsApproved(true);
            $this->addFlash('success', $this->translator->trans('Content is online'));
        } else {
            $comment->setIsApproved(false);
            $this->addFlash('danger', $this->translator->trans('Content is offline'));
        }

        $this->em->persist($comment);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
