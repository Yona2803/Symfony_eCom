<?php

namespace App\Service;

use App\Entity\Items;
use App\Entity\Users;
use App\Entity\WishList;
use App\Repository\ItemsRepository;
use App\Repository\UsersRepository;
use App\Repository\WishListRepository;
use Doctrine\ORM\EntityManagerInterface;

class WishListService
{
    private $em;
    private $wishlistRepository;
    private $itemsRepository;
    private $usersRepository;

    public function __construct(
        EntityManagerInterface $em,
        WishListRepository $wishlistRepository,
        ItemsRepository $itemsRepository,
        UsersRepository $usersRepository
    ) {
        $this->em = $em;
        $this->wishlistRepository = $wishlistRepository;
        $this->itemsRepository = $itemsRepository;
        $this->usersRepository = $usersRepository;
    }

    // Method to add an item to the wishlist
    public function addToWishList(int $userId, int $itemId): bool
    {
        $value = false;
        $user = $this->usersRepository->find($userId);
        $item = $this->itemsRepository->find($itemId);
        $wishList = $user->getWishList();

        if ($wishList !== null) {
            // Check if the item is already in the wishlist
            if (!$wishList->getItem()->contains($item)) {
                $wishList->addItem($item);
                $value = true;
            }
        } else {
            // Create a new wishlist for the user if it doesn't exist
            $wishList = new Wishlist();
            $wishList->setUser($user);
            $wishList->addItem($item);
            $user->setWishList($wishList);
            $value = true;
        }

        $this->em->persist($wishList);
        $this->em->flush();
        return $value;
    }


    // Method to check if the item is in the wishlist
    public function isItemInWishList(int $userId, int $itemId): bool
    {
        $user = $this->usersRepository->find($userId);
        $wishList = $user->getWishList();

        if ($wishList) {
            $checkAlreadyExists = $this->wishlistRepository->findItemInWishList($wishList->getId(), $itemId);
            return $checkAlreadyExists !== null;
        }

        return false;
    }

    // Method to remove an item from the wishlist
    public function removeItemFromWishlist(int $userId, int $itemId): bool
    {
        $user = $this->usersRepository->find($userId);
        $wishlist = $user->getWishList();
        $item = $this->itemsRepository->find($itemId);

        if ($wishlist && $item) {
            $wishlist->removeItem($item);
            $this->em->persist($wishlist);
            $this->em->flush();
            return true;
        }

        return false;
    }


    public function getWishListById(int $wishlistId)
    {
        return $this->wishlistRepository->find($wishlistId);
    }


    public function getWishListByUserID(int $userId)
    {
        $wishList = $this->wishlistRepository->findByUserId($userId);

        return $wishList;
    }
}
