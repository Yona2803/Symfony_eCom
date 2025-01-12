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


    public function addToWishList(int $userId, int $itemId){

        /*$user = new Users();
        $user->setUsername('ahmed');
        $user->setEmail('ahmed@gmail.com');      /// this code just for testing
        $user->setPassword('123');
        $user->setPhoneNumber('0611111111');
        $user->setRole(Roles::CUSTOMER);
        $this->em->persist($user);*/



        $user = $this->em->getRepository(Users::class)->findOneBy(['id' => $userId]);
        $item = $this->em->getRepository(Items::class)->findOneBy(['id' => $itemId]);

        $wishList = $this->wishlistRepository->findByUserId($userId);

        if ($wishList === null) {
            $wishList = new Wishlist();
            $wishList->setUser($user);
            $wishList->addItem($item);
        }else{
            $wishList->getItem()->add($item);
        }

        $item->addWishlist($wishList);

        $this->em->persist($wishList);
        $this->em->flush();

    }

}