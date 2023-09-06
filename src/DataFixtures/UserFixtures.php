<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\ApiTokenGeneratorService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixtures
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var ApiTokenGeneratorService
     */
    private ApiTokenGeneratorService $apiTokenGenerator;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ApiTokenGeneratorService     $apiTokenGenerator
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        ApiTokenGeneratorService $apiTokenGenerator
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->apiTokenGenerator = $apiTokenGenerator;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(User::class, 10, function (User $user) {
            $user
                ->setName($this->faker->firstName)
                ->setEmail($this->faker->email)
                ->setPassword(
                    $this->passwordEncoder->encodePassword($user, 'query')
                )
                ->setApiToken($this->apiTokenGenerator->generate())
                ->setIsVerified(true)
            ;
            if ($this->faker->boolean(30)) {
                $user
                    ->getSubscription()
                    ->setLevel(
                        $this->faker->boolean(30) ? 'PRO' : 'PLUS',
                        new \DateTimeImmutable('+1 week')
                    )
                ;
            }
        });
    }
}
