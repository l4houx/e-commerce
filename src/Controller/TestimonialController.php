<?php

namespace App\Controller;

use App\Repository\TestimonialRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestimonialController extends BaseController
{
    #[Route('/testimonial', name: 'testimonial', methods: ['GET'])]
    public function testimonial(Request $request, TestimonialRepository $testimonialRepository): Response
    {
        $testimonials = $testimonialRepository->findForPagination(
            $request->query->getInt('page', 1)
        );

        return $this->render('testimonial/testimonial-detail.html.twig', compact('testimonials'));
    }
}
