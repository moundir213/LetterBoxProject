<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Base controller class in Symfony
use Symfony\Component\HttpFoundation\Response; // Class to handle HTTP responses
use Symfony\Component\Routing\Annotation\Route; // Annotation for defining routes

/**
 * HomeController is responsible for handling requests to the home page (`/`).
 */
final class HomeController extends AbstractController
{
    /**
     * The homepage route.
     *
     * @return Response Redirects the user either to the anime list page if authenticated or to the login page otherwise.
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Check if the user is fully authenticated
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If the user is logged in, redirect them to the anime list page
            return $this->redirectToRoute('all_anime');
        }

        // If the user is not authenticated, redirect them to the login page
        return $this->redirectToRoute('app_login');
    }
}
