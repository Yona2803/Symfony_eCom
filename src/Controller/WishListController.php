<?php

namespace App\Controller;

use App\Service\WishListService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
        echo $itemId;
        $this->wishListService->addToWishList(4,$itemId);
        return $this->redirectToRoute('productsPage');

    }


}
