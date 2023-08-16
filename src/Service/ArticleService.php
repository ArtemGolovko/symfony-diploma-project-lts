<?php

namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Article $article
     *
     * @return bool
     */
    public function save(Article $article): bool
    {
        $this->em->persist($article);
        $this->em->flush();

        return true;
    }
}
