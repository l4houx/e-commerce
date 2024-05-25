<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\SubCategory;
use App\Entity\Traits\HasRoles;
use App\Form\SubCategoryFormType;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::ADMIN)]
#[Route('/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_sub_category_')]
class SubCategoryController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly SubCategoryRepository $subCategoryRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route(path: '/sub-category', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $rows = $this->subCategoryRepository->findForPagination($page);

        return $this->render('dashboard/admin/products/sub_category/index.html.twig', compact('rows'));
    }

    #[Route(path: '/sub-category/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/sub-category/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $subCategory = new SubCategory();
        } else {
            /** @var SubCategory $subCategory */
            $subCategory = $this->subCategoryRepository->find(['id' => $id]);
            if (!$subCategory) {
                $this->addFlash('danger', $this->translator->trans('The sub category can not be found'));

                return $this->redirectToRoute('dashboard_admin_product_sub_category_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(SubCategoryFormType::class, $subCategory)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($subCategory);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $subCategory->getName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $subCategory->getName()
                        )
                    );
                }

                return $this->redirectToRoute('dashboard_admin_product_sub_category_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/products/sub_category/new-edit.html.twig', compact('form', 'subCategory'));
    }

    #[Route(path: '/sub-category/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(SubCategory $subCategory): Response
    {
        return $this->render('dashboard/admin/products/sub_category/show.html.twig', compact('subCategory'));
    }

    #[Route(path: '/sub-category/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, SubCategory $subCategory): Response
    {
        if ($this->isCsrfTokenValid('subcategory_deletion_'.$subCategory->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($subCategory);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $subCategory->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_product_sub_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
