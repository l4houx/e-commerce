<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\Shipping;
use App\Entity\Traits\HasRoles;
use App\Form\ShippingFormType;
use App\Repository\ShippingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(HasRoles::ADMIN)]
#[Route(path: '/%website_dashboard_path%/admin/manage-products', name: 'dashboard_admin_product_shipping_')]
class ShippingController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ShippingRepository $shippingRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route(path: '/shipping', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $rows = $this->shippingRepository->findForPagination($page);

        return $this->render('dashboard/admin/products/shipping/index.html.twig', compact('rows'));
    }

    #[Route(path: '/shipping/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route(path: '/shipping/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $shipping = new Shipping();
        } else {
            /** @var Shipping $shipping */
            $shipping = $this->shippingRepository->find(['id' => $id]);
            if (!$shipping) {
                $this->addFlash('danger', $this->translator->trans('The shipping can not be found'));

                return $this->redirectToRoute('dashboard_admin_product_shipping_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(ShippingFormType::class, $shipping)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($shipping);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash(
                        'success',
                        sprintf(
                            $this->translator->trans('Content %s was created successfully.'),
                            $shipping->getName()
                        )
                    );
                } else {
                    $this->addFlash(
                        'info',
                        sprintf(
                            $this->translator->trans('Content %s was edited successfully.'),
                            $shipping->getName()
                        )
                    );
                }

                return $this->redirectToRoute('dashboard_admin_product_shipping_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/admin/products/shipping/new-edit.html.twig', compact('form', 'shipping'));
    }

    #[Route(path: '/shipping/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Shipping $shipping): Response
    {
        return $this->render('dashboard/admin/products/shipping/show.html.twig', compact('shipping'));
    }

    #[Route(path: '/shipping/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Shipping $shipping): Response
    {
        if ($this->isCsrfTokenValid('shipping_deletion_'.$shipping->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($shipping);
            $this->em->flush();

            $this->addFlash(
                'danger',
                sprintf(
                    $this->translator->trans('Content %s was deleted successfully.'),
                    $shipping->getName()
                )
            );
        }

        return $this->redirectToRoute('dashboard_admin_product_shipping_index', [], Response::HTTP_SEE_OTHER);
    }
}
