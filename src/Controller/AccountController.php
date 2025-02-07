<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Users;
use App\Entity\Carts;
use App\Entity\CartItems;
use App\Entity\Items;
use App\Form\UsersType;
use App\Service\UsersService;
use App\Service\WishListService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AccountController extends AbstractController
{
    private $usersService;
    private $wishListService;

    public function __construct(
        UsersService $usersService,
        WishListService $wishListService,
    ) {
        $this->wishListService = $wishListService;
        $this->usersService = $usersService;
    }

    // GET Route to display the form
    #[Route('/MyAccount', name: 'MyAccountPage', methods: ['GET'])]
    public function getUserAccount(EntityManagerInterface $entityManager): Response
    {
        $User_id = $this->usersService->getIdOfAuthenticatedUser();

        $Status = "";
        // return new Response('GET.');
        $User_Data = $entityManager->getRepository(Users::class)->find($User_id);
        $form = $this->createForm(UsersType::class, $User_Data);

        return $this->render('Pages/AccountPage/AccountPage.html.twig', [
            'User_Data' => $User_Data,
            'form' => $form->createView(),
            'Status' => $Status,
        ]);
    }

    // POST Route to handle form submission
    #[Route('/MyAccount', name: 'UpdateAccountPage', methods: ['POST'])]
    public function postUserAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $User_id = $this->usersService->getIdOfAuthenticatedUser();

        $Status = "";

        if (!$User_id) {
            $Status = "User_id"; // User Id not found
        } else {
            $User_Data = $entityManager->getRepository(Users::class)->find($User_id);
            if (!$User_Data) {
                $Status = "User_Data"; // User Data not found
            } else {
                $form = $this->createForm(UsersType::class, $User_Data);
                $form->handleRequest($request);

                if ($form->isSubmitted()) {
                    $currentPassword = $form->get('currentPassword')->getData();
                    if ($passwordHasher->isPasswordValid($User_Data, $currentPassword)) {
                        $newPassword = $form->get('newPassword')->getData();
                        if (!empty($newPassword)) {
                            $hashedPassword = $passwordHasher->hashPassword($User_Data, $newPassword);
                            $User_Data->setPassword($hashedPassword);
                            $Status = "hashedPassword"; // New password was updated.
                        }
                        $entityManager->persist($User_Data);
                        $entityManager->flush();
                        $Status = "ok"; // User data updated successfully.
                    } else {
                        $Status = "CurrentPassWord"; // Current password is incorrect.
                    }
                }
            }
        }

        return $this->render('Pages/AccountPage/AccountPage.html.twig', [
            'form' => $form->createView(),
            'User_Data' => $User_Data,
            'Status' => $Status,
        ]);
    }

    // Handle synchronization
    #[Route('/SyncLocalStorage', name: 'SyncLocalStorage', methods: ['POST'])]
    public function syncLocalStorage(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $data = json_decode($request->getContent(), true);

            if (!isset($data['cart']) && !isset($data['wishList'])) {
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

            // Handle wishlist synchronization
            if ($data['wishList']) {
                $this->syncWishList($userId, $data['wishList']);
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Data synced successfully',
                'updatedWishList' => $this->prepareWishListData($userId),
                'updatedCart' => $this->prepareCartData($user, $entityManager)
            ]);
        } catch (AccessDeniedException $e) {
            return new JsonResponse([
                'status' => 'nothing',
                'message' => 'Not conected',
            ], Response::HTTP_OK);
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
    private function syncWishList(int $userId, array $wishListItems): void
    {
        foreach ($wishListItems as $itemData) {
            if (!isset($itemData['id'])) {
                continue;
            }
            $this->wishListService->addToWishList($userId, $itemData['id']);
        }
    }
    // private function prepareWishListData(int $userId): array
    // {
    //     $wishListItems = $this->wishListService->getWishListByUserID($userId);
    //     return array_map(
    //         fn($item) => ['id' => (string) $item->getId()],
    //         $wishListItems->getItem()->toArray()
    //     );
    // }
    private function prepareWishListData(int $userId): array
    {
        try {
            $wishListItems = $this->wishListService->getWishListByUserID($userId);

            if ($wishListItems === null) {
                return [];
            }

            $items = $wishListItems->getItem();

            if ($items === null) {
                return [];
            }

            return array_map(
                fn($item) => [
                    'id' => (string) $item->getId(),
                ],
                $items->toArray()
            );
        } catch (\Exception $e) {
            return [];
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
    #[Route('/GetWishList/{value}', name: 'GetAll')]
    public function index(string $value): JsonResponse
    {

        $wishlistData = $this->wishListService->getWishListByUserID($value);
        $itemIds = [];

        foreach ($wishlistData->getItem() as $item) {
            // $itemIds[] = (string) $item->getId(); 
            $itemIds[] = [
                'id' => (string) $item->getId(),
            ];
        }

        // Return the item IDs in JSON format
        return new JsonResponse(['item_ids' => $itemIds]);

        return new JsonResponse([
            'wishlist' => [
                'id' => $wishlistData->getId(),
                'userId' => $wishlistData->getUser()->getId(),
                'items' => array_map(function ($item) {
                    return [
                        'id' => $item->getId(),
                        'name' => $item->getName(), // Example of item fields
                        'description' => $item->getDescription(),
                    ];
                }, $wishlistData->getItem()->toArray())
            ]
        ]);
    }

    // **** hash passwords ****
    #[Route('/Hash/{Password}', name: 'test_hash')]
    public function testHash(string $Password, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Create a dummy user
        $user = new Users();
        // Hash the $Password
        $hashed = $passwordHasher->hashPassword($user, $Password);

        return new Response("Hashed Password: " . $hashed);
    }
}
