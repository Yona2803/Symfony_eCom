<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Service\WishListService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class WishListController extends AbstractController
{

    private $params;
    private WishListService $wishListService;
    private UsersRepository $usersRepository;

    public function __construct(
        WishListService $wishListService,
        UsersRepository $usersRepository,
        ParameterBagInterface $params
    ) {
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




    #[Route('/wishlist/delete/{itemId}', name: 'delete_item', methods: 'DELETE')]
    public function deleteItem(int $itemId): JsonResponse
    {
        $success = $this->wishListService->removeItemFromWishlist(itemId: $itemId);

        if ($success) {
            $this->addFlash('success', 'Product successfully removed from your wishlist.');
            return new JsonResponse(['status' => 'success', 'message' => 'Product successfully removed from your wishlist.'], Response::HTTP_OK);
        }

        $this->addFlash('error', 'Product not found or could not be removed.');
        return new JsonResponse(['status' => 'error', 'message' => 'Product not found or could not be removed.'], Response::HTTP_NOT_FOUND);
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
}
