<?php

namespace App\Controller\Dashboard\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Service\AvatarService;
use App\Entity\Traits\HasRoles;
use App\Service\SuspendedService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Exception\PremiumNotBanException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/%website_dashboard_path%/admin/manage-users', name: 'dashboard_admin_user_')]
#[IsGranted(HasRoles::ADMINAPPLICATION)]
class UserController extends AdminBaseController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $em,
        private readonly AvatarService $avatarService,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $rows = $this->userRepository->findAll();

        return $this->render('dashboard/admin/user/index.html.twig', compact('rows'));
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $form->has('plainPassword') ? $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ) : ''
            );

            $avatar = $this->avatarService->createAvatar($user->getEmail());
            $user->setAvatar($avatar);
            $user->setLastLoginIp($request->getClientIp());
            //$user->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('Content was created successfully.'));

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/admin/user/new.html.twig', compact('user', 'form'));
    }

    #[Route(path: '/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('user_deletion_'.$user->getId(), $request->request->get('_token'))) {
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('danger', $this->translator->trans('Content was deleted successfully.'));
        }

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/restore', name: 'restore', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function restore(string $slug): Response
    {
        /** @var User $user */
        $user = $this->userRepository->findUsers();
        if (!$user) {
            $this->addFlash('danger', $this->translator->trans('The user can not be found'));

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        /*
        $user->setDeletedAt(null);

        $this->em->persist($user);
        $this->em->flush();
        */

        $this->addFlash('success', $this->translator->trans('Content was restored successfully.'));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/enable', name: 'enable', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[Route(path: '/{id}/disable', name: 'disable', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function enabledisable(): Response
    {
        $user = $this->userRepository->findUsers();
        if (!$user) {
            $this->addFlash('danger', $this->translator->trans('The user can not be found'));

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        /* @var User $user */
        /*if ($user->isVerified()) {
            $user->setIsVerified(false);
            if ($user->hasRole(HasRoles::RESTAURANT)) {
                foreach ($user->getRestaurant()->getScanners() as $scanner) {
                    $scanner->getUser()->setIsVerified(false);
                    $this->em->persist($scanner->getUser());
                }
                foreach ($user->getRestaurant()->getPointofsales() as $pointofsale) {
                    $pointofsale->getUser()->setIsVerified(false);
                    $this->em->persist($pointofsale->getUser());
                }
            }
            $this->addFlash('info', $this->translator->trans('The user has been disabled'));
        } else {
            $user->setIsVerified(true);
            if ($user->hasRole(HasRoles::RESTAURANT)) {
                foreach ($user->getRestaurant()->getScanners() as $scanner) {
                    $scanner->getUser()->setIsVerified(true);
                    $this->em->persist($scanner->getUser());
                }
                foreach ($user->getRestaurant()->getPointofsales() as $pointofsale) {
                    $pointofsale->getUser()->setIsVerified(true);
                    $this->em->persist($pointofsale->getUser());
                }
            }
            $this->addFlash('success', $this->translator->trans('The user has been enabled'));
        }

        $this->em->persist($user);
        $this->em->flush();
        */

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/suspended', name: 'suspended', methods: ['POST', 'DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function suspended(Request $request, User $user, SuspendedService $suspendedService): Response
    {
        $username = $user->getUsername();

        try {
            $suspendedService->suspended($user);
            $this->em->flush();
        } catch (PremiumNotBanException) {
            $this->addFlash('danger', $this->translator->trans('Unable to suspended a premium user.'));

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        $this->addFlash('success', $this->translator->trans("User $username has been suspended"));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/verified', name: 'verified', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function verified(User $user): RedirectResponse
    {
        $user->setIsVerified(true);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('The account has been verified successfully.'));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/more-information', name: 'information', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function informations(Request $request): Response
    {
        $user = $this->userRepository->findAll();
        if (!$user) {
            $this->addFlash('danger', $this->translator->trans('The user can not be found'));

            return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/admin/user/information.html.twig', compact('user'));
    }

    #[Route(path: '/{id}/change-role', name: 'change_role', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function changeRole(User $user): Response
    {
        $user->setRoles([HasRoles::EDITOR, HasRoles::DEFAULT]);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('The editor role has been successfully added to your user.'));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{id}/remove-role', name: 'remove_role', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function removeRole(User $user): Response
    {
        $user->setRoles([]);
        $this->em->flush();

        $this->addFlash('danger', $this->translator->trans('The editor role has been successfully remove to your user.'));

        return $this->redirectToRoute('dashboard_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/search', name: 'autocomplete')]
    public function search(Request $request): JsonResponse
    {
        $this->userRepository = $this->em->getRepository(User::class);

        $q = strtolower($request->query->get('q') ?: '');
        if ('moi' === $q) {
            return new JsonResponse([
                [
                    'id' => $this->getUser()->getId(),
                    'username' => $this->getUser()->getUsername(),
                ],
            ]);
        }

        $users = $this->userRepository
            ->createQueryBuilder('u')
            ->select('u.id', 'u.username')
            ->where('LOWER(u.username) LIKE :username')
            ->setParameter('username', "%$q%")
            ->setMaxResults(25)
            ->getQuery()
            ->getResult()
        ;

        return new JsonResponse($users);
    }
}
