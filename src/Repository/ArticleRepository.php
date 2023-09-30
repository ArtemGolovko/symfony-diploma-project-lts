<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
     */
    public function findHoursCountByAuthor(User $author): int
    {
        return $this->countByAuthorAfterDate($author, new \DateTimeImmutable('-1 day'));
    }

    /**
     * @param User $author
     *
     * @return Query
     */
    public function findByAuthorQuery(User $author): Query
    {
        return $this
            ->createQueryBuilder('a')
            ->andWhere('a.author = :author')
            ->setParameter('author', $author)
            ->orderBy('a.createdAt', 'desc')
            ->getQuery()
        ;
    }

    /**
     * @param User $author
     *
     * @return int
     */
    public function findMouthsCountByAuthor(User $author): int
    {
        return $this->countByAuthorAfterDate($author, new \DateTimeImmutable('-1 month'));
    }

    /**
     * @param User $author
     *
     * @return int
     */
    public function findTotalCountbyAuthor(User $author): int
    {
        return $this
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.author = :author')
            ->setParameter('author', $author)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param User $author
     *
     * @return Article|null
     */
    public function findLatestByAuthor(User $author): ?Article
    {
        return $this
            ->createQueryBuilder('a')
            ->andWhere('a.author = :author')
            ->setParameter('author', $author)
            ->orderBy('a.createdAt', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param User               $author
     * @param \DateTimeImmutable $date
     *
     * @return int
     */
    private function countByAuthorAfterDate(User $author, \DateTimeImmutable $date): int
    {
        return $this
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.author = :author and a.createdAt > :date')
            ->setParameters([
                'author' => $author,
                'date' => $date,
            ])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
