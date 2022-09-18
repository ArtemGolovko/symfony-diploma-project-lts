<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixtures
{


    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(User::class, 5, function (User $user) {
           $user
               ->setName($this->faker->firstName)
               ->setEmail($this->faker->email)
               ->setPassword(
                   $this->passwordEncoder->encodePassword($user, 'query')
               )
           ;
        });
    }
}
