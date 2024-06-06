<?php

namespace App\Controller\Dashboard\Shared;

use App\Controller\BaseController;
use App\Entity\Product;
use App\Entity\Review;
use App\Entity\Traits\HasLimit;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Form\ReviewFormType;
use App\Service\SettingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::DEFAULT)]
class ReviewController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly SluggerInterface $slugger,
        private readonly SettingService $settingService
    ) {
    }

    #[Route(path: '/%website_dashboard_path%/user/my-reviews', name: 'dashboard_user_review_index', methods: ['GET'])]
    #[Route(path: '/%website_dashboard_path%/admin/manage-reviews', name: 'dashboard_admin_review_index', methods: ['GET'])]
    public function index(Request $request, AuthorizationCheckerInterface $authChecker, PaginatorInterface $paginator): Response
    {
        $keyword = '' == $request->query->get('keyword') ? 'all' : $request->query->get('keyword');
        $product = '' == $request->query->get('product') ? 'all' : $request->query->get('product');
        $isVisible = '' == $request->query->get('isVisible') ? 'all' : $request->query->get('isVisible');
        $rating = '' == $request->query->get('rating') ? 'all' : $request->query->get('rating');
        $slug = '' == $request->query->get('slug') ? 'all' : $request->query->get('slug');

        $user = 'all';
        if ($authChecker->isGranted(HasRoles::DEFAULT)) {
            $user = $this->getUser()->getId();
        }

        $reviews = $paginator->paginate(
            $this->settingService->getReviews(['user' => $user, 'keyword' => $keyword, 'product' => $product, 'slug' => $slug, 'isVisible' => $isVisible, 'rating' => $rating])->getQuery(),
            $request->query->getInt('page', 1),
            HasLimit::REVIEW_LIMIT,
            ['wrap-queries' => true]
        );

        return $this->render('dashboard/shared/review/index.html.twig', compact('reviews', 'user'));
    }

    #[Route(path: '/%website_dashboard_path%/user/my-reviews/{slug}/new', name: 'dashboard_user_review_new', methods: ['GET', 'POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function new(Request $request, #[CurrentUser] User $user, string $slug, UrlGeneratorInterface $url): Response
    {
        /** @var Product $product */
        $product = $this->settingService->getProducts(['slug' => $slug, 'elapsed' => 'all'])->getQuery()->getOneOrNullResult();
        if (!$product) {
            $this->addFlash('danger', $this->translator->trans('The product not be found'));

            return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
        }

        $review = new Review();

        $form = $this->createForm(ReviewFormType::class, $review)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $review->setSlug($this->slugger->slug($review->getName())->lower());
                $review->setAuthor($user);
                $review->setProduct($product);

                $this->em->persist($review);
                $this->em->flush();

                $this->addFlash('success', $this->translator->trans('Your review has been successfully saved'));

                return $this->redirect($url->generate('product', ['id' => $product->getId()]).'#reviews');
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/review/new.html.twig', compact('form', 'review', 'product'));
    }

    #[Route(path: '/%website_dashboard_path%/admin/manage-reviews/{slug}/show', name: 'dashboard_admin_review_show', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[Route(path: '/%website_dashboard_path%/admin/manage-reviews/{slug}/hide', name: 'dashboard_admin_review_hide', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function showhide(string $slug): Response
    {
        /** @var Review $review */
        $review = $this->settingService->getReviews(['slug' => $slug, 'isVisible' => 'all'])->getQuery()->getOneOrNullResult();
        if (!$review) {
            $this->addFlash('danger', $this->translator->trans('The review can not be found'));

            return $this->redirectToRoute('dashboard_admin_review_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($review->getIsVisible()) {
            $review->setIsVisible(false);
            $this->addFlash('success', $this->translator->trans('Content is online'));
        } else {
            $review->setIsVisible(true);
            $this->addFlash('danger', $this->translator->trans('Content is offline'));
        }

        $this->em->persist($review);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_review_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/%website_dashboard_path%/admin/manage-reviews/{slug}/delete-permanently', name: 'dashboard_admin_review_delete_permanently', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[Route(path: '/%website_dashboard_path%/admin/manage-reviews/{slug}/delete', name: 'dashboard_admin_review_delete', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function delete(string $slug): Response
    {
        /** @var Review $review */
        $review = $this->settingService->getReviews(['slug' => $slug, 'isVisible' => 'all'])->getQuery()->getOneOrNullResult();
        if (!$review) {
            $this->addFlash('danger', $this->translator->trans('The review can not be found'));

            return $this->redirectToRoute('dashboard_admin_review_index', [], Response::HTTP_SEE_OTHER);
        }

        if (null !== $review->getDeletedAt()) {
            $this->addFlash('danger', $this->translator->trans('Content was deleted permanently successfully.'));
        } else {
            $this->addFlash('danger', $this->translator->trans('Content was deleted successfully.'));
        }

        $review->setIsVisible(false);

        $this->em->persist($review);
        $this->em->flush();
        $this->em->remove($review);
        $this->em->flush();

        return $this->redirectToRoute('dashboard_admin_review_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/%website_dashboard_path%/admin/manage-reviews/{slug}/restore', name: 'dashboard_admin_review_restore', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function restore(string $slug): Response
    {
        /** @var Review $review */
        $review = $this->settingService->getReviews(['slug' => $slug, 'isVisible' => 'all'])->getQuery()->getOneOrNullResult();
        if (!$review) {
            $this->addFlash('danger', $this->translator->trans('The review can not be found'));

            return $this->redirectToRoute('dashboard_admin_review_index', [], Response::HTTP_SEE_OTHER);
        }

        $review->setDeletedAt(null);

        $this->em->persist($review);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('Content was restored successfully.'));

        return $this->redirectToRoute('dashboard_admin_review_index', [], Response::HTTP_SEE_OTHER);
    }
}
