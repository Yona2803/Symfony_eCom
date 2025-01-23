<?php

namespace App\Service;

use App\Entity\Users;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UsersService
{
    private $tokenStorage;




    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getIdOfAuthenticatedUser(): int
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        if ($user instanceof Users) {
            $userId = $user->getId();
        }
        return $userId;
    }


}
