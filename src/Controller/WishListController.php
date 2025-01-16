<?php

namespace App\Controller;

use App\Service\WishListService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class WishListController extends AbstractController
{

    private WishListService $wishListService;

    public function __construct(WishListService $wishListService)
    {
        $this->wishListService = $wishListService;
    }


    #[Route('/addItemToWishList/{itemId}', name: 'toWishlist', methods: ['GET'])]
    public function addItemToWishList(int $itemId): Response
    {
        // replace the number 9 with an id of a user in your database
        $value = $this->wishListService->addToWishList(9, $itemId);

        if ($value) {
            $this->addFlash('success', 'Item added to wishlist successfully.');
        } else {
            $this->addFlash('warning', 'Item already exists in wishlist.');
        }

        return $this->redirectToRoute('productsPage');
    }



    #[Route('/wishlist', name: 'wishlistPage', methods: ['GET'])]
    public function wishList(): Response
    {
        // replace the number 9 with an id of a user in your database
        $wishlist = $this->wishListService->getWishListById(13);

        return $this->render('items/wishlistPage.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }


    #[Route('/delete_item/{itemId}', name: 'delete_item', methods: ['GET'])]
    public function removeItemFromWishList(int $itemId): Response
    {
        $value = $this->wishListService->removeFromWishList($itemId);

        if ($value) {
            $this->addFlash('success', 'Item removed from wishlist successfully.');
        } else {
            $this->addFlash('warning', 'Item does not exist in wishlist.');
        }

        return $this->redirectToRoute('dashboard');
    }


}
