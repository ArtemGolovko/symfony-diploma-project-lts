<?php

namespace App\Controller\Dashboard;

use App\Entity\Article;
use App\Entity\Dto\PromotedWord;
use App\Entity\User;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Subscription;
use App\Form\CreateArticleFormType;
use App\Repository\ArticleRepository;
use App\Service\ArticleContentGenerator\ArticleContentGenerator;
use App\Service\ArticleService;
use App\Service\ImageUploadService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use League\Flysystem\FilesystemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @param Request                 $request
     * @param ArticleContentGenerator $articleContentGenerator
     * @param ArticleService          $articleService
     * @param ImageUploadService      $imageUploadService
     *
     * @return Response
     * @throws LoaderError
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws SyntaxError
     * @throws FilesystemException
     */
    public function create(
        Request $request,
        ArticleContentGenerator $articleContentGenerator,
        ArticleService $articleService,
        ImageUploadService $imageUploadService
    ): Response {
        $formData = null;
        if ($request->query->has('id') && $request->isMethod(Request::METHOD_GET)) {
            $article = $this->getDoctrine()->getManager()->find(Article::class, $request->query->getInt('id'));
            $formData = $article->getGenerateOptions();
        }

        $form = $this->createForm(CreateArticleFormType::class, $formData);
        $form->handleRequest($request);

        if ($request->query->has('id') && !$form->isSubmitted()) {
            $article = $this->getDoctrine()->getManager()->find(Article::class, $request->query->getInt('id'));
            $form->setData($article->getGenerateOptions());
        }

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

            $data->setImages(
                array_map(function (UploadedFile $file) use ($imageUploadService) {
                    return $imageUploadService->upload($file);
                }, $form->get('images')->getData())
            );

            $data->setPromotedWords($promotedWords);
            /** @var User $user */
            $user = $this->getUser();

            $article = $articleContentGenerator->generate(
                $data,
                $user->getSubscription()->getLevel() !== Subscription::FREE
            );

            $article = (new Article())
                ->setTitle($article['title'])
                ->setContent($article['content'])
                ->setGenerateOptions($data)
                ->setAuthor($user)
            ;

            if (!$articleService->save($article)) {
                $request->getSession()->getFlashBag()->set('error', true);

                return $this->redirectToRoute('app_dashboard_article_create');
            };

            $session->set('article_content', $article->getContent());

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
     * @param Request            $request
     * @param ArticleRepository  $articleRepository
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function history(
        Request $request,
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator
    ): Response {
        $articles = $articleRepository->findByAuthorQuery($this->getUser());
        $pagination = $paginator->paginate($articles, $request->query->getInt('page', 1), 10);

        return $this->render('dashboard/article/history.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/dashboard/articles/{id}", name="app_dashboard_article_show")
     * @param Article $article
     *
     * @return Response
     */
    public function show(Article $article): Response
    {
        return $this->render('dashboard/article/show.html.twig', [
            'article' => $article,
        ]);
    }
}
