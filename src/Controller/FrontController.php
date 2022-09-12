<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(): Response
    {
        return $this->render('front/homepage.html.twig');
    }

    /**
     * @Route("/demo", name="app_demo")
     */
    public function demo(): Response
    {
        return $this->render('front/demo.html.twig');
    }
}
