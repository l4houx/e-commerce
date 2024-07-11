<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/%website_admin_path%', name: 'admin_')]
class SecurityController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(path: '/signin', name: 'signin', methods: ['GET', 'POST'])]
    public function signin(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('danger', $this->translator->trans('Already logged in'));

            return $this->redirectToRoute('admin_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'page_title' => $this->getParameter('website_name'),
            'username_label' => $this->translator->trans('Email'),
            'sign_in_label' => $this->translator->trans('Log in'),
            'error' => $error,
            'last_username' => $lastUsername,
            'csrf_token_intention' => 'authenticate',
            'username_parameter' => 'username',
            'password_parameter' => 'password',
        ]);
    }

    #[Route(path: '/signout', name: 'signout', methods: ['GET', 'POST'])]
    /** @codeCoverageIgnore */
    public function logout(): void
    {
    }
}
