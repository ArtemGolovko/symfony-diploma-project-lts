<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends BaseFixtures
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(User::class, 10, function (User &$user) {
            $user = $this->userService->create(
                $this->faker->email,
                $this->faker->firstName,
                'query',
                true
            );

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
