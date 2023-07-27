<?php

namespace App\Service\ArticleContentGenerator;

use App\Repository\ModuleRepository;
use Symfony\Component\Security\Core\Security;

class DoctrineModuleProvider implements ModuleProviderInterface
{
    /**
     * @var ModuleRepository
     */
    private ModuleRepository $moduleRepository;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param ModuleRepository $moduleRepository
     * @param Security         $security
     */
    public function __construct(ModuleRepository $moduleRepository, Security $security)
    {
        $this->moduleRepository = $moduleRepository;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function getModules(): array
    {
        $user = $this->security->getUser();

        if (null === $user) {
            return [];
        }

        return $this->moduleRepository->findBy(['author' => $user]);
    }
}