<?php

namespace App\Controller\API;

use App\Controller\BaseController;
use App\Entity\Shop\AddressCustoner;
use App\Repository\Shop\AddressCustonerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiAddressController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AddressCustonerRepository $addressCustonerRepository
    ) {
    }

    #[Route('/address', name: 'app_post_address', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $user = $this->getUserOrThrow();

        if (!$user) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Not authorization !',
                'data' => [],
            ]);
        }

        $formData = $request->getPayload();

        $addresscustoner = new AddressCustoner();
        $addresscustoner
            ->setName($formData->get('name'))
            ->setClientName($formData->get('clientName'))
            ->setStreet($formData->get('street'))
            ->setStreet2($formData->get('street2'))
            ->setPostalcode($formData->get('postalcode'))
            ->setCity($formData->get('city'))
            ->setCountryCode($formData->get('countrycode'))
            ->setUser($user)
        ;

        $this->em->persist($addresscustoner);
        $this->em->flush();

        $addresscustoneres = $this->addressCustonerRepository->findByUser($user);

        foreach ($addresscustoneres as $key => $addresscustoner) {
            $addresscustoner->setUser(null);
            $addresscustoneres[$key] = $addresscustoner;
        }

        return $this->json([
            'isSuccess' => true,
            'data' => $addresscustoneres,
        ]);
    }

    #[Route('/address/{id}', name: 'app_api_put_address', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {
        $user = $this->getUserOrThrow();

        if (!$user) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Not authorization !',
                'data' => [],
            ]);
        }

        $addresscustoner = $this->addressCustonerRepository->findOneById($id);

        if (!$addresscustoner) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Address not found !',
                'data' => [],
            ]);
        }

        if ($user !== $addresscustoner->getUser()) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Not authorization !',
                'data' => [],
            ]);
        }

        $formData = $request->getPayload();
        $addresscustoner
            ->setName($formData->get('name'))
            ->setClientName($formData->get('clientName'))
            ->setStreet($formData->get('street'))
            ->setStreet2($formData->get('street2'))
            ->setPostalcode($formData->get('postalcode'))
            ->setCity($formData->get('city'))
            ->setCountryCode($formData->get('countrycode'))
        ;

        $this->em->persist($addresscustoner);
        $this->em->flush();

        $addresscustoneres = $this->addressCustonerRepository->findByUser($user);

        foreach ($addresscustoneres as $key => $addresscustoner) {
            $addresscustoner->setUser(null);
            $addresscustoneres[$key] = $addresscustoner;
        }

        return $this->json([
            'isSuccess' => true,
            'data' => $addresscustoneres,
        ]);
    }

    #[Route('/address/{id}', name: 'app_api_delete_address', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $user = $this->getUserOrThrow();

        if (!$user) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Not authorization !',
                'data' => [],
            ]);
        }

        $addresscustoner = $this->addressCustonerRepository->findOneById($id);

        if (!$addresscustoner) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Address not found !',
                'data' => [],
            ]);
        }

        if ($user !== $addresscustoner->getUser()) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Not authorization !',
                'data' => [],
            ]);
        }

        $this->em->remove($addresscustoner);
        $this->em->flush();

        $addresscustoneres = $this->addressCustonerRepository->findByUser($user);
        foreach ($addresscustoneres as $key => $addresscustoner) {
            $addresscustoner->setUser(null);
            $addresscustoneres[$key] = $addresscustoner;
        }

        return $this->json([
            'isSuccess' => true,
            'data' => $addresscustoneres,
        ]);
    }
}
