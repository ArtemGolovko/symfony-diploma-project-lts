<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard(): Response
    {
        return new Response("stub");
    }

    /**
     * @Route("/dashdoard/subscription", name="app_dashboard_subscription")
     */
    public function subscription(): Response
    {
        return new Response("stub");
    }

    /**
     * @Route("/dashboard/profile", name="app_dashboard_profile")
     */
    public function profile(): Response
    {
        return new Response("stub");
    }

    /**
     * @Route("/dashboard/modules", name="app_dashboard_modules")
     */
    public function modules(): Response
    {
        return new Response("stub");
    }
}
