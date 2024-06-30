<?php

namespace App\Controller\Dashboard\Shared\Shop;

use App\Controller\BaseController;
use App\Entity\Shop\Product;
use App\Entity\Shop\Review;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Form\Shop\ReviewFormType;
use App\Repository\Shop\ProductRepository;
use App\Repository\Shop\ReviewRepository;
use App\Security\Voter\ReviewVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class ReviewController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly ReviewRepository $reviewRepository,
        private readonly ProductRepository $productRepository
    ) {
    }

    #[Route(path: '/my-reviews', name: 'review_index', methods: ['GET'])]
    public function index(#[CurrentUser] User $user): Response
    {
        return $this->render('dashboard/shared/shop/review/index.html.twig', [
            'user' => $user,
            'reviews' => $this->reviewRepository->getLastByUser($user, 1),
        ]);
    }

    #[Route(path: '/my-reviews/{slug}/new', name: 'review_new', methods: ['GET', 'POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    #[IsGranted(ReviewVoter::CREATE)]
    public function new(Request $request, #[CurrentUser] User $user, UrlGeneratorInterface $url): Response
    {
        /** @var Product $product */
        $product = $this->productRepository->findOneBy(['slug' => $request->get('slug')]);
        if (!$product) {
            $this->addFlash('danger', $this->translator->trans('The product not be found'));

            return $this->redirectToRoute('shop', [], Response::HTTP_SEE_OTHER);
        }

        $review = new Review();

        $form = $this->createForm(ReviewFormType::class, $review)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $review->setAuthor($user);
                $review->setProduct($product);

                $this->em->persist($review);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    sprintf(
                        $this->translator->trans('Your review %s has been successfully sent.'),
                        $review->getAuthor()->getFullName()
                    )
                );

                return $this->redirect($url->generate('shop_product', ['slug' => $product->getSlug()]).'#reviews');
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/shop/review/new.html.twig', compact('form', 'review', 'user', 'product'));
    }
}
