<?php

namespace App\Service;

use App\Entity\Module;
use App\Entity\User;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class ModuleService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var ModuleRepository
     */
    private ModuleRepository $moduleRepository;

    /**
     * @param EntityManagerInterface $em
     * @param ModuleRepository       $moduleRepository
     */
    public function __construct(EntityManagerInterface $em, ModuleRepository $moduleRepository)
    {
        $this->em = $em;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * @param User $user
     *
     * @return Query
     */
    public function getModulesForUserQuery(User $user): Query
    {
        return $this->moduleRepository->findByAuthorQuery($user);
    }

    /**
     * @param Module $module
     *
     * @return void
     */
    public function save(Module $module): void
    {
        $this->em->persist($module);
        $this->em->flush();
    }

    /**
     * @param Module $module
     *
     * @return void
     */
    public function remove(Module $module): void
    {
        $this->em->remove($module);
        $this->em->flush();
    }
}
