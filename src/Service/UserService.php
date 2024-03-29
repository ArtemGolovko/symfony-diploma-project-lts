<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var ApiTokenGenerator
     */
    private ApiTokenGenerator $apiTokenGenerator;

    /**
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ApiTokenGenerator            $apiTokenGenerator
     */
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        ApiTokenGenerator $apiTokenGenerator
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->apiTokenGenerator = $apiTokenGenerator;
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $password
     * @param bool   $isVerified
     * @param bool   $flush
     *
     * @return User
     */
    public function create(
        string $email,
        string $name,
        string $password,
        bool $isVerified = false,
        bool $flush = true
    ): User {
        $user = new User();
        $user
            ->setEmail($email)
            ->setName($name)
            ->setPassword($this->passwordEncoder->encodePassword($user, $password))
            ->setApiToken($this->apiTokenGenerator->generate())
            ->setIsVerified($isVerified)
        ;

        $this->em->persist($user);
        if ($flush) {
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function regenerateApiToken(User $user): void
    {
        $user->setApiToken($this->apiTokenGenerator->generate());
        $this->em->flush();
    }
}
