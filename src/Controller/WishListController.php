<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Service\WishListService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;


class WishListController extends AbstractController
{

    private $params;
    private WishListService $wishListService;
    private UsersRepository $usersRepository;

    public function __construct(
        WishListService $wishListService,
        UsersRepository $usersRepository,
        ParameterBagInterface $params)
    {
        $this->wishListService = $wishListService;
        $this->usersRepository = $usersRepository;
        $this->params = $params;
    }



    #[Route('/addItemToWishList/{itemId}', name: 'toWishlist', methods: ['GET'])]
    public function addItemToWishList(int $itemId): Response
    {
        $userId = $this->params->get('user_id');
        $value = $this->wishListService->addToWishList($userId, $itemId);

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

        $userId = $this->params->get('user_id');
        $wishlist = $this->usersRepository->find($userId)->getWishList();

        return $this->render('items/wishlistPage.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }



    #[Route('/wishlist/delete/{itemId}', name: 'delete_item', methods: 'DELETE')]
    public function deleteItem(int $itemId): Response
    {
        $success = $this->wishListService->removeItemFromWishlist($itemId);

        if ($success) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_NOT_FOUND);
    }

    
}
