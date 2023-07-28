<?php

namespace App\Service\ArticleContentGenerator;

use App\Entity\Module;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Security;

/**
 * User's module provider
 */
class UserModuleProvider implements ModuleProviderInterface
{
    /**
     * @var Security
     */
    private Security $security;

    private ObjectRepository $moduleRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Security               $security
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->security = $security;
        $this->moduleRepository = $entityManager->getRepository(Module::class);
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