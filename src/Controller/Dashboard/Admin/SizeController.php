<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Size;
use App\Entity\Traits\HasRoles;
use App\Form\SizeFormType;
use App\Repository\SizeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::ADMIN)]
#[Route('/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_size_')]
class SizeController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly SizeRepository $sizeRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route(path: '/size', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/admin/products/size/index.html.twig', [
            'rows' => $this->sizeRepository->findAll(),
        ]);
    }

    #[Route(path: '/size/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/size/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $size = new Size();
        } else {
            /** @var Size $size */
            $size = $this->sizeRepository->find(['id' => $id]);
            if (!$size) {
                $this->addFlash('danger', $this->translator->trans('The size can not be found'));

                return $this->redirectToRoute('dashboard_admin_product_size_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(SizeFormType::class, $size)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($size);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $size->getName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $size->getName()
                        )
                    );
                }

                return $this->redirectToRoute('dashboard_admin_product_size_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/products/size/new-edit.html.twig', compact('form', 'size'));
    }

    #[Route(path: '/size/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Size $size): Response
    {
        return $this->render('dashboard/admin/products/size/show.html.twig', compact('size'));
    }

    #[Route(path: '/size/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Size $size): Response
    {
        if ($this->isCsrfTokenValid('size_deletion_'.$size->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($size);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $size->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_product_size_index', [], Response::HTTP_SEE_OTHER);
    }
}
