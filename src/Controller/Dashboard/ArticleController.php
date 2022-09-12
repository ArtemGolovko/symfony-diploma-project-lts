<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/dashboard/articles/create", name="app_dashboard_article_create")
     */
    public function create(): Response
    {
        return new Response("stub");
    }

    /**
     * @Route("/dashboard/history", name="app_dashboard_history")
     */
    public function history(): Response
    {
        return new Response("stub");
    }

    /**
     * @Route("/dashboard/articles/{id}", name="app_dashboard_article_show")
     */
    public function show(): Response
    {
        return new Response("stub");
    }
}
