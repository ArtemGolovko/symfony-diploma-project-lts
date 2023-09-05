<?php

namespace App\Controller;

use App\Form\CreateDemoArticleFormType;
use App\Service\ArticleContentGenerator\ArticleContentGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

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
     * @param Request                 $request
     * @param ArticleContentGenerator $articleContentGenerator
     *
     * @return Response
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function demo(Request $request, ArticleContentGenerator $articleContentGenerator): Response
    {
        $cookies = $request->cookies;

        $content = $cookies->get('content');

        $form = $this->createForm(CreateDemoArticleFormType::class, [
            'title' => $cookies->get('title'),
            'promotedWord' => $cookies->get('promotedWord'),
        ]);

        $form->handleRequest($request);

        if (!$content && $form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $content = $articleContentGenerator->generateDemo($formData['title'], $formData['promotedWord']);

            $response = $this->redirectToRoute('app_demo');

            $response->headers->setCookie(Cookie::create('title', $formData['title'], 2147483647));
            $response->headers->setCookie(Cookie::create('promotedWord', $formData['promotedWord'], 2147483647));
            $response->headers->setCookie(Cookie::create('content', $content, 2147483647));

            return $response;
        }

        return $this->render('front/demo.html.twig', [
            'form' => $form->createView(),
            'content' => $content,
        ]);
    }
}
