<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_ANONYMOUS")
 */
class FrontController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     * @return Response
     */
    public function homepage(): Response
    {
        return $this->render('front/homepage.html.twig');
    }

    /**
     * @Route("/demo", name="app_demo")
     * @return Response
     */
    public function demo(): Response
    {
        return $this->render('front/demo.html.twig');
    }
}
