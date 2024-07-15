<?php

namespace App\Controller\Dashboard\Shared;

use App\Controller\BaseController;
use App\Entity\Shop\AddressCustoner;
use App\Entity\Traits\HasRoles;
use App\Form\AddressCustonerFormType;
use App\Repository\Shop\AddressCustonerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/%website_dashboard_path%/account', name: 'dashboard_account_')]
#[IsGranted(HasRoles::DEFAULT)]
class AddressCustonerController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AddressCustonerRepository $addressCustonerRepository,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route('/address', name: 'address_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUserOrThrow();
        $addresses = $this->addressCustonerRepository->findByUser($user);

        return $this->render('dashboard/shared/address/index.html.twig', compact('addresses'));
    }

    #[Route(path: '/address/new', name: 'address_new', methods: ['GET', 'POST'])]
    #[Route(path: '/address/{id}/edit', name: 'address_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function newedit(Request $request, ?int $id = null): Response
    {
        if (!$id) {
            $address = new AddressCustoner();
        } else {
            /** @var AddressCustoner $address */
            $address = $this->addressCustonerRepository->find($id);
            if (!$address) {
                $this->addFlash('danger', $this->translator->trans('The address can not be found'));

                return $this->redirectToRoute('dashboard_account_address_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        $form = $this->createForm(AddressCustonerFormType::class, $address)->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->persist($address);
                $this->em->flush();
                if (!$id) {
                    $this->addFlash('success', $this->translator->trans('The address has been created successfully.'));
                } else {
                    $this->addFlash('danger', $this->translator->trans('The address has been edited successfully.'));
                }

                return $this->redirectToRoute('dashboard_account_address_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $this->translator->trans('The form contains invalid data'));
            }
        }

        return $this->render('dashboard/shared/address/new-edit.html.twig', compact('form', 'address'));
    }

    #[Route('/{id}', name: 'address_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(AddressCustoner $address): Response
    {
        return $this->render('dashboard/shared/address/show.html.twig', compact('address'));
    }

    #[Route('/{id}', name: 'address_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, AddressCustoner $address): Response
    {
        if ($this->isCsrfTokenValid('address_deletion_'.$address->getId(), $request->getPayload()->get('_token'))) {
            $this->em->remove($address);
            $this->em->flush();

            $this->addFlash('danger', $this->translator->trans('The address has been deleted successfully.'));
        }

        return $this->redirectToRoute('dashboard_account_address_index', [], Response::HTTP_SEE_OTHER);
    }
}
