<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Color;
use App\Entity\Traits\HasRoles;
use App\Form\ColorFormType;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::ADMIN)]
#[Route('/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_color_')]
class ColorController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ColorRepository $colorRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route(path: '/color', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $rows = $this->colorRepository->findForPagination($page);

        return $this->render('dashboard/admin/products/color/index.html.twig', compact('rows'));
    }

    #[Route(path: '/color/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/color/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $color = new Color();
        } else {
            /** @var Color $color */
            $color = $this->colorRepository->find(['id' => $id]);
            if (!$color) {
                $this->addFlash('danger', $this->translator->trans('The color can not be found'));

                return $this->redirectToRoute('dashboard_admin_product_color_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(ColorFormType::class, $color)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($color);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $color->getName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $color->getName()
                        )
                    );
                }

                return $this->redirectToRoute('dashboard_admin_product_color_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/products/color/new-edit.html.twig', compact('form', 'color'));
    }

    #[Route(path: '/color/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Color $color): Response
    {
        return $this->render('dashboard/admin/products/color/show.html.twig', compact('color'));
    }

    #[Route(path: '/color/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Color $color): Response
    {
        if ($this->isCsrfTokenValid('color_deletion_'.$color->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($color);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $color->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_product_color_index', [], Response::HTTP_SEE_OTHER);
    }
}
