<?php

namespace App\Controller;


use App\Repository\UsersRepository;
use App\Service\UsersService;
use App\Service\WishListService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class WishListController extends AbstractController
{

    private $wishListService;
    private $usersRepository;
    private $usersService;

    public function __construct(
        WishListService $wishListService,
        UsersRepository $usersRepository,
        ParameterBagInterface $params,
        UsersService $usersService
    ) {
        $this->wishListService = $wishListService;
        $this->usersRepository = $usersRepository;
        $this->usersService = $usersService;
    }



    #[Route('/wishlist/add/{itemId}', name: 'toWishlist', methods: ['GET'])]
    public function addItemToWishList(int $itemId): Response
    {

        $userId = $this->usersService->getIdOfAuthenticatedUser();
        $value = $this->wishListService->addToWishList($userId, $itemId);

        if ($value) {
            $this->addFlash('addToWishlist', 'Product successfully added to your wishlist.');
            return new JsonResponse(['status' => 'addToWishlist', 'message' => 'Product successfully added to your wishlist.'], Response::HTTP_OK);
        }

        $this->addFlash('wishlistError', 'Product already exist in your wishlist.');
        return new JsonResponse(['status' => 'wishlistError', 'message' => 'Product already exist in your wishlist.'], Response::HTTP_BAD_REQUEST);
    }




    #[Route('/wishlist/delete/{itemId}', name: 'delete_item', methods: 'DELETE')]
    public function deleteItem(int $itemId): JsonResponse
    {

        $userId = $this->usersService->getIdOfAuthenticatedUser();

        $success = $this->wishListService->removeItemFromWishlist($userId, $itemId);

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

        $userId = $this->usersService->getIdOfAuthenticatedUser();
        $user = $this->usersRepository->find($userId);

        if (!$user) {
            return $this->render('items/wishlistPage.html.twig', [
                'wishlist' => [],
            ]); 
        }
    
        $wishlist = $user->getWishList();
        return $this->render('items/wishlistPage.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }
    
}
