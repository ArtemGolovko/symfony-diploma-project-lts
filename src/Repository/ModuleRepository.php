<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ModuleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function findByAuthorQuery(User $author)
    {
        return $this
            ->createQueryBuilder('m')
            ->andWhere('m.author = :author')
            ->setParameter('author', $author)
            ->orderBy('m.createdAt', 'desc')
            ->getQuery()
        ;
    }
}
