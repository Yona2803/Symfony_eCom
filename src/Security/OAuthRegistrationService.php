<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Users;
use App\Repository\UsersRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

final readonly class OAuthRegistrationService
{
    /**
     * @param GoogleUser $resourceOwner
     */
    public function persist(ResourceOwnerInterface $resourceOwner, UsersRepository $repository): Users
    {
        $user = (new Users())
            ->setEmail($resourceOwner->getEmail())
            ->setGoogleId($resourceOwner->getId())
            ->setRoles(['ROLE_CUSTOMER'])
            ->setUsername($resourceOwner->getName())
            ->setFirstName($resourceOwner->getFirstName())
            ->setLastName($resourceOwner->getLastName());

        $repository->add($user, true);
        return $user;
    }
}