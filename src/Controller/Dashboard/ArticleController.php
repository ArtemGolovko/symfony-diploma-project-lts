<?php

namespace App\Controller\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_AND_VERIFIED")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/dashboard/articles/create", name="app_dashboard_article_create")
     */
    public function create(): Response
    {
        return $this->render('dashboard/article/create.html.twig');
    }

    /**
     * @Route("/dashboard/history", name="app_dashboard_history")
     */
    public function history(): Response
    {
        return $this->render('dashboard/article/history.html.twig');
    }

    /**
     * @Route("/dashboard/articles/{id}", name="app_dashboard_article_show")
     */
    public function show(): Response
    {
        return $this->render('dashboard/article/show.html.twig');
    }
}
