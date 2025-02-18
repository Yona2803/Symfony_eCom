<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
    }


    public function load(ObjectManager $manager): void
    {
        $user = new Users();
        $user->setFirstName('hassan');
        $user->setEmail('hassan@gmail.com');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, '123456'));
        $manager->persist($user);

        $manager->flush();
    }


    public function getGroups(): array
    {
        return ['users'];
    }
}
