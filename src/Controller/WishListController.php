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


    #[Route('/addItemToWishList/{itemId}', name: 'toWishlist')]
    public function addItemToWishList(int $itemId): Response
    {
        $value = $this->wishListService->addToWishList(4,$itemId);

        if ($value) {
            $this->addFlash('success', 'Item added to wishlist successfully.');
        }else{
            $this->addFlash('warning', 'Item already exists in wishlist.');
        }

        return $this->redirectToRoute('productsPage');
    }


}
