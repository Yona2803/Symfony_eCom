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

//use Symfony\Component\Security\Http\Attribute\IsGranted;


class WishListController extends AbstractController
{

    private $wishListService;
    private $usersRepository;
    private $usersService;

    public function __construct(
        WishListService $wishListService,
        UsersRepository $usersRepository,
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

        // Respond with error if not authenticated
        return new JsonResponse([
            'status' => 'wishlistError',
            'message' => 'User Not Authenticated yet.'
        ], Response::HTTP_BAD_REQUEST); // Changed to HTTP_BAD_REQUEST

    }




    #[Route('/wishlist/delete/{itemId}', name: 'delete_item', methods: 'DELETE')]
    public function deleteItem(int $itemId): JsonResponse
    {

        $userId = $this->usersService->getIdOfAuthenticatedUser();

        $success = $this->wishListService->removeItemFromWishlist($userId, $itemId);

        if ($success) {
            return new JsonResponse(
                [
                    'status' => 'successRemoving',
                    'message' => 'Product successfully removed from your wishlist.'
                ],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            [
                'status' => 'errorRemoving',
                'message' => 'Product not found or could not be removed.'
            ],
            Response::HTTP_NOT_FOUND
        );
    }




    #[Route('/wishlist', name: 'wishlistPage', methods: ['GET'])]
    public function wishList(): Response
    {

        if($this->getUser()){
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
        return $this->redirect('Connecting/LogIn');
    }


}
