<?php

namespace App\Controller\Dashboard;

use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Form\CreateArticleFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_AND_VERIFIED")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/dashboard/articles/create", name="app_dashboard_article_create")
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(CreateArticleFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleGenerateOptions $data */
            $data = $form->getData();

            dd($data);
        }

        return $this->render('dashboard/article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/history", name="app_dashboard_history")
     * @return Response
     */
    public function history(): Response
    {
        return $this->render('dashboard/article/history.html.twig');
    }

    /**
     * @Route("/dashboard/articles/{id}", name="app_dashboard_article_show")
     * @return Response
     */
    public function show(): Response
    {
        return $this->render('dashboard/article/show.html.twig');
    }
}
