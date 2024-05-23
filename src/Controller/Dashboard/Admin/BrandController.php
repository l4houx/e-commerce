<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Brand;
use App\Entity\Traits\HasRoles;
use App\Form\BrandFormType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::ADMIN)]
#[Route('/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_brand_')]
class BrandController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly BrandRepository $brandRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route(path: '/brand', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/admin/products/brand/index.html.twig', [
            'rows' => $this->brandRepository->findAll(),
        ]);
    }

    #[Route(path: '/brand/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/brand/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $brand = new Brand();
        } else {
            /** @var Brand $brand */
            $brand = $this->brandRepository->find(['id' => $id]);
            if (!$brand) {
                $this->addFlash('danger', $this->translator->trans('The brand can not be found'));

                return $this->redirectToRoute('dashboard_admin_product_brand_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(BrandFormType::class, $brand)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($brand);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $brand->getName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $brand->getName()
                        )
                    );
                }

                return $this->redirectToRoute('dashboard_admin_product_brand_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/products/brand/new-edit.html.twig', compact('form', 'brand'));
    }

    #[Route(path: '/brand/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Brand $brand): Response
    {
        return $this->render('dashboard/admin/products/brand/show.html.twig', compact('brand'));
    }

    #[Route(path: '/brand/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Brand $brand): Response
    {
        if ($this->isCsrfTokenValid('brand_deletion_'.$brand->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($brand);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $brand->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_product_brand_index', [], Response::HTTP_SEE_OTHER);
    }
}
