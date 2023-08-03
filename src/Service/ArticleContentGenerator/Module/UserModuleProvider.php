<?php

namespace App\Service\ArticleContentGenerator\Module;

use App\Entity\Module;
use App\Entity\User;
use App\Entity\ValueObject\Subscription;
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
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (null === $user || $user->getSubscription()->getLevel() !== Subscription::PRO) {
            return [];
        }

        return $this->moduleRepository->findBy(['author' => $user]);
    }
}
