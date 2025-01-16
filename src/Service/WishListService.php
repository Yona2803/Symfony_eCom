<?php

namespace App\Service;

use App\Entity\Items;
use App\Entity\Users;
use App\Entity\WishList;
use App\Repository\ItemsRepository;
use App\Repository\UsersRepository;
use App\Repository\WishListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WishListService
{

    private EntityManagerInterface $em;
    private WishListRepository $wishlistRepository;
    private ItemsRepository $itemsRepository;
    private UsersRepository $usersRepository;
    private $params;

    public function __construct(
        EntityManagerInterface $em,
        WishListRepository $wishlistRepository,
        ItemsRepository $itemsRepository,
        UsersRepository $usersRepository,
        ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->wishlistRepository = $wishlistRepository;
        $this->itemsRepository = $itemsRepository;
        $this->usersRepository = $usersRepository;
        $this->params = $params;
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


    public function removeItemFromWishlist(int $itemId): bool
    {
        $item = $this->itemsRepository->find($itemId);
        $userId = $this->params->get('user_id');

        if ($item) {
            $wishlist = $this->usersRepository->find($userId)->getWishList();
            if ($wishlist) {
                $wishlist->removeItem($item);
                $this->em->persist($wishlist);
                $this->em->flush();
                return true;
            }
        }

        return false;
    }


}
