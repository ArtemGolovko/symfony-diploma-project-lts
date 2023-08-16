<?php

namespace App\Controller\Dashboard;

use App\Entity\Article;
use App\Entity\Dto\PromotedWord;
use App\Entity\User;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Subscription;
use App\Form\CreateArticleFormType;
use App\Service\ArticleContentGenerator\ArticleContentGenerator;
use App\Service\ArticleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

/**
 * @IsGranted("IS_AUTHENTICATED_AND_VERIFIED")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/dashboard/articles/create", name="app_dashboard_article_create")
     * @param Request $request
     * @param ArticleContentGenerator $articleContentGenerator
     *
     * @return Response
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function create(
        Request $request,
        ArticleContentGenerator $articleContentGenerator,
        ArticleService $articleService
    ): Response {
        $form = $this->createForm(CreateArticleFormType::class);
        $form->handleRequest($request);

        $session = $request->getSession();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArticleGenerateOptions $data */
            $data = $form->getData();
            $promotedWordsRaw = $request->request->get("create_article_form")["promoted_words"];
            $promotedWords = array_filter(
                array_map(function (array $promotedWordRaw): PromotedWord {
                    return new PromotedWord($promotedWordRaw['word'], (int)$promotedWordRaw['repetitions']);
                }, $promotedWordsRaw),
                function (PromotedWord $promotedWord): bool {
                    return !$promotedWord->isEmpty();
                }
            );

            $data->setPromotedWords($promotedWords);
            /** @var User $user */
            $user = $this->getUser();

            $article = $articleContentGenerator->generate(
                $data,
                $user->getSubscription()->getLevel() !== Subscription::FREE
            );

            $session->set('article_content', $article['content']);

            $article = (new Article())
                ->setTitle($article['title'])
                ->setContent($article['content'])
                ->setGenerateOptions($data)
                ->setAuthor($user)
            ;

            $articleService->save($article);

            return $this->redirectToRoute('app_dashboard_article_create');
        }
        $content = $session->get('article_content');
        $session->remove('article_content');

        return $this->render('dashboard/article/create.html.twig', [
            'form' => $form->createView(),
            'content' => $content,
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
