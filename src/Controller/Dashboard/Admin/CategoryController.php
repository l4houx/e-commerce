<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Entity\Traits\HasRoles;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(HasRoles::ADMIN)]
#[Route('/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_category_')]
class CategoryController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route(path: '/categories', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('dashboard/admin/products/category/index.html.twig', [
            'rows' => $this->categoryRepository->findAll(),
        ]);
    }

    #[Route(path: '/categories/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/categories/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $category = new Category();
        } else {
            /** @var Category $category */
            $category = $this->categoryRepository->find(['id' => $id]);
            if (!$category) {
                $this->addFlash('danger', $this->translator->trans('The category can not be found'));

                return $this->redirectToRoute('dashboard_admin_product_category_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(CategoryFormType::class, $category)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($category);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $category->getName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $category->getName()
                        )
                    );
                }

                return $this->redirectToRoute('dashboard_admin_product_category_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/products/category/new-edit.html.twig', compact('form', 'category'));
    }

    #[Route(path: '/categories/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Category $category): Response
    {
        return $this->render('dashboard/admin/products/category/show.html.twig', compact('category'));
    }

    #[Route(path: '/categories/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('category_deletion_'.$category->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($category);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $category->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_product_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
