<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
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
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $user = $this->userService->create($faker->email, $faker->firstName, 'query', true, false);

            if ($faker->boolean(30)) {
                $user
                    ->getSubscription()
                    ->setLevel(
                        $faker->boolean(30) ? 'PRO' : 'PLUS',
                        new \DateTimeImmutable('+1 week')
                    )
                ;
            }

            $this->addReference(User::class . '|' . $i, $user);
        }
        $manager->flush();
    }
}
