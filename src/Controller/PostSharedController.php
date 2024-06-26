<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostSharedFormType;
use App\Service\SendMailService;
use App\Service\SettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostSharedController extends AbstractController
{
    #[Route('/post/{slug}/shared', name: 'post_shared', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['GET', 'POST'])]
    public function postShared(
        Request $request,
        Post $post,
        SendMailService $mail,
        SettingService $settingService,
        TranslatorInterface $translator
    ): Response {
        if (!$post) {
            $this->addFlash('danger', $translator->trans('The article not be found'));

            return $this->redirectToRoute('posts');
        }

        $appErrors = [];

        $form = $this->createForm(PostSharedFormType::class);

        if (0 == $settingService->getSettings('google_recaptcha_enabled')) {
            $form->remove('recaptcha');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $subject = sprintf('%s advises you to read "%s"', $data['sender_name'], $post->getName());

            $mail->send(
                $this->getParameter('website_no_reply_email'),
                $data['receiver_email'],
                $subject,
                'post-shared',
                [
                    'post' => $post,
                    'sender_name' => $data['sender_name'],
                    'sender_comments' => $data['sender_comments'],
                ],
            );

            $this->addFlash('success', $translator->trans('🚀 Post successfully shared with your friend!'));

            return $this->redirectToRoute('posts', [], Response::HTTP_SEE_OTHER);
        } elseif ($form->isSubmitted()) {
            /** @var FormError $error */
            foreach ($form->getErrors() as $error) {
                if (null === $error->getCause()) {
                    $appErrors[] = $error;
                }
            }
        }

        return $this->render('post/post-shared.html.twig', [
            'errors' => $appErrors,
            'post' => $post,
            'form' => $form,
        ]);
    }
}
