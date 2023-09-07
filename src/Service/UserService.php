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
     * @var ApiTokenGeneratorService
     */
    private ApiTokenGeneratorService $apiTokenGenerator;

    /**
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ApiTokenGeneratorService     $apiTokenGenerator
     */
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        ApiTokenGeneratorService $apiTokenGenerator
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
     *
     * @return User
     */
    public function create(string $email, string $name, string $password, bool $isVerified = false): User
    {
        $user = new User();
        $user
            ->setEmail($email)
            ->setName($name)
            ->setPassword($this->passwordEncoder->encodePassword($user, $password))
            ->setApiToken($this->apiTokenGenerator->generate())
            ->setIsVerified($isVerified)
        ;

        return $user;
    }

    /**
     * @param User $user
     * @param bool $flush
     *
     * @return void
     */
    public function save(User $user, bool $flush = true): void
    {
        $this->em->persist($user);

        if ($flush) {
            $this->em->flush();
        }
    }
}