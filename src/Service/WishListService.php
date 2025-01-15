<?php

namespace App\Service;

use App\Entity\Items;
use App\Entity\Roles;
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
}
