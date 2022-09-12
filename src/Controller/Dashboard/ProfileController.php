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
        return $this->render('dashboard/profile/dashboard.html.twig');
    }

    /**
     * @Route("/dashboard/subscription", name="app_dashboard_subscription")
     */
    public function subscription(): Response
    {
        return $this->render('dashboard/profile/subscription.html.twig');
    }

    /**
     * @Route("/dashboard/profile", name="app_dashboard_profile")
     */
    public function profile(): Response
    {
        return $this->render('dashboard/profile/profile.html.twig');
    }

    /**
     * @Route("/dashboard/modules", name="app_dashboard_modules")
     */
    public function modules(): Response
    {
        return $this->render('dashboard/profile/modules.html.twig');
    }
}
