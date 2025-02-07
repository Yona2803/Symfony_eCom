<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;
use App\Entity\Carts;
use App\Entity\CartItems;
use App\Entity\Users;
use App\Service\UsersService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MailerController extends AbstractController
{
    private $usersService;

    public function __construct(
        UsersService $usersService,
    ) {
        $this->usersService = $usersService;
    }


    #[Route('/DeleteAll', name: 'DeleteAll')] //methods: ['DELETE]'
    public function myCartDelete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            // Fetch the logged-in user directly instead of querying via user ID.
            $userId = $this->usersService->getIdOfAuthenticatedUser();
            $user = $entityManager->getRepository(Users::class)->find($userId);

            if (!$user) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'User not found'
                ]);
            }

            // Attempt to delete the user's cart and cart items.
            $this->deleteUserCarts($user, $entityManager);

            return new JsonResponse(['status' => 'success', 'message' => 'Cart deleted successfully'], 200);
        } catch (\Exception $e) {
            error_log("Error in myCartDelete: " . $e->getMessage());
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    private function deleteUserCarts(Users $user, EntityManagerInterface $entityManager): void
    {
        $cart = $entityManager->getRepository(Carts::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            throw new \Exception("Cart not found for user ID: " . $user->getId());
        }
        
        $cartItems = $entityManager->getRepository(CartItems::class)->findBy(['cart' => $cart]);
        
        // Remove each CartItems entry
        foreach ($cartItems as $cartItem) {
            $entityManager->remove($cartItem);
        }

        // Finally, remove the cart itself
        $entityManager->remove($cart);
        
        // Persist the changes to the database
        $entityManager->flush();

    }
}
