<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\ValueObject\Subscription;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ArticleService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var ArticleRepository
     */
    private ArticleRepository $articleRepository;

    /**
     * @param EntityManagerInterface $em
     * @param ArticleRepository      $articleRepository
     */
    public function __construct(EntityManagerInterface $em, ArticleRepository $articleRepository)
    {
        $this->em = $em;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param Article $article
     *
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function save(Article $article): bool
    {
        $author = $article->getAuthor();
        $count = $this->articleRepository->findHoursCountByAuthor($author);

        if ($author->getSubscription()->getLevel() !== Subscription::PRO && $count >= 2) {
            return false;
        }

        $this->em->persist($article);
        $this->em->flush();

        return true;
    }
}
