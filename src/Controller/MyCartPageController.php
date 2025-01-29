<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\Items;
use App\Entity\Users;
use App\Entity\Carts;
use App\Entity\CartItems;
use App\Service\UsersService;
use App\Service\CartService;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\DTO\SyncStorageRequest;

class MyCartPageController extends AbstractController
{
    private $usersService;
    private $CartService;

    public function __construct(
        UsersService $usersService,
    ) {
        $this->usersService = $usersService;
    }

    #[Route('/MyCart', name: 'MyCartPage')]
    public function cart()
    {
        return $this->render('Pages/CartPage/CartPage.html.twig');
    }

    #[Route('/MyCart/ShowProducts', name: 'ShowProducts', methods: ['GET'])]
    public function ShowProducts(Request $request, EntityManagerInterface $entityManager)
    {
        $itemsIds = $request->query->get('items_ids');

        if (!$itemsIds) {
            return new Response('no items in the URL', 200);
        }

        $decodedItems = json_decode($itemsIds, true);
        // Check if decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new Response('Invalid JSON format', 400);
        }

        // 2nd stage : getting items from db
        $products = $entityManager->getRepository(Items::class)->findBy(['id' => $decodedItems]);

        if (!$products) {
            return new Response('DB: no data');
        }

        // preparing the Array response
        $productDetails = [];

        foreach ($products as $product) {
            $itemImage = $product->getItemImage();
            if (is_resource($itemImage)) {
                $itemImage = stream_get_contents($itemImage);
            }
            $base64Image = base64_encode($itemImage);

            $productDetails[] = [
                [
                    'id' => $product->getId(),
                    'itemImage' => 'data:image/jpg;base64,' . $base64Image,
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'stock' => $product->getStock()
                ]
            ];
        }

        if (!$productDetails) {
            return new Response('Products : no data');
        }

        return new JsonResponse($productDetails);
    }


    // **** Add Cart to DB ****
    #[Route('/MyCartItems', name: 'myCartItems', methods: ['POST'])]
    public function myCartItems(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $data = json_decode($request->getContent(), true);

            if (!isset($data['cart'])) {
                return new JsonResponse(['error' => 'Invalid data'], 400);
            }

            $userId = $this->usersService->getIdOfAuthenticatedUser();
            $user = $entityManager->getRepository(Users::class)->find($userId);

            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }

            // Handle cart synchronization
            if ($data['cart']) {
                $this->syncCart($user, $data['cart'], $entityManager);
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Data synced successfully',
                'updatedCart' => $this->prepareCartData($user, $entityManager)
            ]);
        
        } catch (NotFoundHttpException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while syncing data',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
    private function syncCart(Users $user, array $cartItems, EntityManagerInterface $entityManager): void
    {
        $cart = $entityManager->getRepository(Carts::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = $this->createNewCart($user, $entityManager);
        }

        foreach ($cartItems as $itemData) {
            if (!isset($itemData['id'], $itemData['quantity'])) {
                continue;
            }

            $this->updateCartItem($cart, $itemData, $entityManager);
        }

        $entityManager->flush();
    }
    private function createNewCart(Users $user, EntityManagerInterface $entityManager): Carts
    {
        $cart = new Carts();
        $cart->setUser($user);
        $entityManager->persist($cart);

        return $cart;
    }
    private function updateCartItem(Carts $cart, array $itemData, EntityManagerInterface $entityManager): void
    {
        $item = $entityManager->getRepository(Items::class)->find($itemData['id']);
        if (!$item) {
            return;
        }

        $existingCartItem = $entityManager->getRepository(CartItems::class)
            ->findOneBy(['cart' => $cart, 'item' => $item]);

        if ($existingCartItem) {
            $existingCartItem->setQuantity($itemData['quantity']);
        } else {
            $newCartItem = new CartItems();
            $newCartItem->setCart($cart);
            $newCartItem->setItem($item);
            $newCartItem->setQuantity($itemData['quantity']);
            $entityManager->persist($newCartItem);
        }
    }
    private function prepareCartData(Users $user, EntityManagerInterface $entityManager): array
    {
        $cartItems = $entityManager->getRepository(CartItems::class)
            ->findBy(['cart' => $entityManager->getRepository(Carts::class)->findOneBy(['user' => $user])]);

        return array_map(
            fn($cartItem) => [
                'id' => (string) $cartItem->getItem()->getId(),
                'quantity' => (string) $cartItem->getQuantity(),
            ],
            $cartItems
        );
    }

    // **** Testing ****
    #[Route('/GetCart/{userId}', name: 'GetCartAll')]
    public function getCartAll(int $userId, EntityManagerInterface $entityManager): JsonResponse
    {
        // Fetch the user from the database
        $user = $entityManager->getRepository(Users::class)->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Check if the user already has a cart
        $CartData = $entityManager->getRepository(Carts::class)->findOneBy(['user' => $user]);

        if (!$CartData) {
            return new JsonResponse(['error' => 'Cart not found for the given user'], 404);
        }

        // Fetch all cart items associated with the cart


        $cartItems = $entityManager->getRepository(CartItems::class)
            ->findBy(['cart' => $entityManager->getRepository(Carts::class)->findOneBy(['user' => $user])]);

        $test[] = array_map(
            fn($cartItem) => [
                'id' => (string) $cartItem->getItem()->getId(),
                'quantity' => (string) $cartItem->getQuantity(),
            ],
            $cartItems
        );

        return new JsonResponse($test);
    }

    #[Route('/cart/{userId}/{itemId}/{quantity}', name: 'addToCart')]
    public function addToCart(int $userId, int $itemId, int $quantity, EntityManagerInterface $entityManager): JsonResponse
    {
        // Validate input data
        if (!$userId || !$itemId || !$quantity || $quantity <= 0) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        // Fetch the user from the database
        $user = $entityManager->getRepository(Users::class)->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Check if the user already has a cart
        $cart = $entityManager->getRepository(Carts::class)->findOneBy(['user' => $user]);

        // If the user doesn't have a cart, create a new one
        if (!$cart) {
            $cart = new Carts();
            $cart->setUser($user);
            $entityManager->persist($cart);
            $entityManager->flush(); // Persist the cart to the database
        }

        // Fetch the item
        $item = $entityManager->getRepository(Items::class)->find($itemId);

        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], 404);
        }

        // Check if the item is already in the cart
        $existingCartItem = $entityManager->getRepository(CartItems::class)
            ->findOneBy(['cart' => $cart, 'item' => $item]);

        if ($existingCartItem) {
            // If the item already exists in the cart, update the quantity
            $existingCartItem->setQuantity($existingCartItem->getQuantity() + $quantity);
        } else {
            // Otherwise, create a new cart item
            $cartItem = new CartItems();
            $cartItem->setCart($cart);
            $cartItem->setItem($item);
            $cartItem->setQuantity($quantity);
            $entityManager->persist($cartItem);
        }

        // Persist changes to the database
        $entityManager->flush();

        return new JsonResponse(['message' => 'Item added to cart successfully']);
    }

    #[Route('/testSend', name: 'testSend', methods: ['POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        #[MapRequestPayload] SyncStorageRequest $data
    ): JsonResponse {

        $test[] =  [
            ["id" => "1", "quantity" => "1"],
            ["id" => "2", "quantity" => "2"]
        ];

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Data synced successfully',
            'updatedCart' => $test,
        ]);
    }
}
