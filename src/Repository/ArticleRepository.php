<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param User $author
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findHoursCountByAuthor(User $author): int
    {
        return $this
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.author = :author and a.createdAt > :date')
            ->setParameters([
                'author' => $author,
                'date' => new \DateTimeImmutable('-1 hour'),
            ])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
