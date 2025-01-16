<?php

namespace App\Service;

use App\Entity\Items;
use App\Entity\Users;
use App\Entity\WishList;
use App\Repository\WishListRepository;
use Doctrine\ORM\EntityManagerInterface;

class WishListService
{

    private EntityManagerInterface $em;
    private WishListRepository $wishlistRepository;
    public function __construct(EntityManagerInterface $em, WishListRepository $wishlistRepository)
    {
        $this->em = $em;
        $this->wishlistRepository = $wishlistRepository;
    }


    public function addToWishList(int $userId, int $itemId): bool
    {
        $value = false;

        $user = $this->em->getRepository(Users::class)->findOneBy(['id' => $userId]);
        $item = $this->em->getRepository(Items::class)->findOneBy(['id' => $itemId]);

        $wishList = $user->getWishList();

        if ($wishList !== null) {
            $checkAlreadyExists = $this->wishlistRepository->findItemInWishList($wishList->getId(), $itemId);
            if (!$checkAlreadyExists) {
                $wishList->getItem()->add($item);
                $value = true;
            }
        } else {
            $wishList = new Wishlist();
            $wishList->setUser($user);
            $wishList->addItem($item);
            $value = true;
        }

        $item->addWishlist($wishList);

        $this->em->persist($wishList);
        $this->em->flush();
        return $value;
    }


    public function getWishListById(int $wishlistId){
        return $this->wishlistRepository->find($wishlistId);
    }


    public function removeFromWishList(int $itemId){
        $value = false;
        $item = $this->em->getRepository(Items::class)->findOneBy(['id' => $itemId]);
        $user = $this->em->getRepository(Users::class)->findOneBy(['id' => 9]);  // replace number 9 with the userId
        $wishList = $user->getWishList();

        if($wishList){
            $wishList->getItem()->removeElement($item);
            $this->em->persist($wishList);
            $this->em->flush();
            $value = true;
        }
        return $value;
    }



}
