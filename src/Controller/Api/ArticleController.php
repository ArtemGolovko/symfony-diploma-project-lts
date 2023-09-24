<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\ValueObject\Subscription;
use App\Service\ArticleContentGenerator\ArticleContentGenerator;
use App\Service\ArticleOptionsDeserializer;
use App\Service\ArticleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1", name="api_v1_")
 * @IsGranted("IS_AUTHENTICATED_FULLY_AND_VERIFIED")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", methods="POST", name="article_create")
     *
     * @param Request                    $request
     * @param ArticleOptionsDeserializer $deserializer
     * @param ArticleContentGenerator    $contentGenerator
     * @param ArticleService             $articleService
     *
     * @return Response
     */
    public function index(
        Request $request,
        ArticleOptionsDeserializer $deserializer,
        ArticleContentGenerator $contentGenerator,
        ArticleService $articleService
    ): Response {
        $options = $deserializer->deserializeJson($request->getContent());

        $generated = $contentGenerator->generate(
            $options,
            $this->getUser()->getSubscription()->getLevel() !== Subscription::FREE
        );

        $article = (new Article())
            ->setTitle($generated['title'])
            ->setContent($generated['content'])
            ->setAuthor($this->getUser())
            ->setGenerateOptions($options)
        ;

        $articleService->save($article);

        $generated['description'] = $articleService->generateDescription($article);

        return $this->json($generated, 200, [
            'Accept: application/json',
        ]);
    }
}
