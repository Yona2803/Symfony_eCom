<?php

namespace App\Controller;


use App\Repository\UsersRepository;
use App\Service\UsersService;
use App\Service\WishListService;
use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


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



    #[Route('/toggleWishlist/{itemId}', name: 'toWishlist')]
    public function toggleWishlist(int $itemId): JsonResponse
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $userId = $this->usersService->getIdOfAuthenticatedUser();
            $isInWishlist = $this->wishListService->isItemInWishList($userId, $itemId);

            if ($isInWishlist) {
                // Remove item from wishlist
                $this->wishListService->removeItemFromWishlist($userId, $itemId);
                return new JsonResponse([
                    'status' => 'removeFromWishlist',
                    'message' => 'Product successfully removed from your wishlist.'
                ], Response::HTTP_OK);
            } else {
                // Add item to wishlist
                $this->wishListService->addToWishList($userId, $itemId);
                return new JsonResponse([
                    'status' => 'addToWishlist',
                    'message' => 'Product successfully added to your wishlist.'
                ], Response::HTTP_OK);
            }
        }
        // Empty Response if not authenticated
        return new JsonResponse([], Response::HTTP_OK);
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
