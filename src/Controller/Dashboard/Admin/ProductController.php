<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductFormType;
use App\Entity\Traits\HasRoles;
use App\Service\ProductService;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted(HasRoles::ADMIN)]
#[Route('/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ProductRepository $productRepository,
        private readonly ProductService $productService,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/admin/products/product/index.html.twig', [
            'rows' => $this->productRepository->findAll(),
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $product = new Product();
            $form = $this->createForm(ProductFormType::class, $product, ['validation_groups' => ['create', 'Default']]);
        } else {
            /** @var Product $product */
            $product = $this->productRepository->find(['id' => $id]);
            if (!$product) {
                $this->addFlash('danger', $this->translator->trans('The product can not be found'));

                return $this->redirectToRoute('dashboard_admin_product_index', [], Response::HTTP_SEE_OTHER);
            }

            $form = $this->createForm(ProductFormType::class, $product, ['validation_groups' => ['update', 'Default']]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                foreach ($product->getImages() as $image) {
                    $image->setProduct($product);
                }

                if (!$id) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $product->getName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $product->getName()
                        )
                    );
                }

                $this->em->persist($product);
                $this->em->flush();

                return $this->redirectToRoute('dashboard_admin_product_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/products/product/new-edit.html.twig', compact('form', 'product'));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Product $product): Response
    {
        return $this->render('dashboard/admin/products/product/show.html.twig', compact('product'));
    }

    #[Route(path: '/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('product_deletion_'.$product->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($product);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $product->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
