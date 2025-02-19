<?php

namespace App\Faker;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;


class UsersFaker
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $em,
    ) {}


    public function createRandomUsers(int $count = 10)
    {
        // Initialize Faker
        $faker = Factory::create();

        for ($i = 0; $i < $count; $i++) {
            $user = new Users();
            $user->setUsername($faker->username);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setEmail($faker->unique()->email);
            $user->setPhoneNumber('06' . $faker->numerify('########'));
            $user->setRoles(['ROLE_CUSTOMER', 'ROLE_ADMIN']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, '123456')); // Default password

            $this->em->persist($user);
        }

        $this->em->flush();
    }
}
