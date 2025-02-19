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
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Items;
use App\Entity\WishList;
use App\Entity\CartItems;
use App\Entity\Users;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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


    #[Route('/wishlist', name: 'wishlist', methods: 'GET')]
    public function index()
    {
        return $this->render('Pages/WishlistPage/WishlistPage.html.twig');
    }



    #[Route('/wishlist/ShowItems', name: 'ShowItems_Wishlist', methods: ['GET'])]
    public function ShowItems(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $itemsIds = $request->query->get('items_ids');

        if (!$itemsIds) {
            return new Response('No items in the URL', 200);
        }

        $decodedItems = json_decode($itemsIds, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new Response('Invalid JSON format', 400);
        }

        // Fetching products from database
        $products = $entityManager->getRepository(Items::class)->findBy(['id' => $decodedItems]);

        if (!$products) {
            return new Response('DB: no data', 404);
        }

        // Building response array
        $productDetails = [];

        foreach ($products as $product) {
            $itemImage = $product->getItemImage();
            if (is_resource($itemImage)) {
                $itemImage = stream_get_contents($itemImage);
            }

            $base64Image = $itemImage ? 'data:image/jpg;base64,' . base64_encode($itemImage) : null;

            $productDetails[] = [
                'id' => $product->getId(),
                'itemImage' => $base64Image,
                'name' => $product->getName(),
                'price' => $product->getPrice(),
            ];
        }

        return new JsonResponse($productDetails);
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
        return new JsonResponse([], Response::HTTP_OK);
    }



    // **** Delete Single item ****
    #[Route('/wishlist/Delete', name: 'myWishlistDelete', methods: ['DELETE'])]
    public function mywWishlistDelete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            // Get the item ID from the query parameter
            $itemId = (int) $request->query->get('item');

            if (!$itemId) {
                return new JsonResponse(['error' => 'Invalid item ID'], 400);
            }

            // Get the authenticated user
            $userId = $this->usersService->getIdOfAuthenticatedUser();
            $user = $entityManager->getRepository(Users::class)->find($userId);

            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }

            // Delete the single item from the cart
            $this->wishListService->removeItemFromWishlist($userId, $itemId);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Item deleted successfully',
            ]);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while deleting the item: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }



    // Hassan Code : below
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

        if ($this->getUser()) {
            $userId = $this->usersService->getIdOfAuthenticatedUser();
            $user = $this->usersRepository->find($userId);

            if (!$user) {
                return $this->render('MyPages/Wishlist/wishlistPage.html.twig', [
                    'wishlist' => [],
                ]);
            }

            $wishlist = $user->getWishList();
            return $this->render('MyPages/Wishlist/wishlistPage.html.twig', [
                'wishlist' => $wishlist,
            ]);
        }
        return $this->redirect('Connecting/LogIn');
    }
}
