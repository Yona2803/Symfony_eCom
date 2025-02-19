<?php

namespace App\Controller;

use App\Faker\UsersFaker;
use App\Repository\UsersRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class UsersController extends AbstractController
{

    public function __construct(
        private UsersRepository $usersRepository,
        private UsersFaker $usersFaker
    ) {
        $this->usersRepository = $usersRepository;
        $this->usersFaker = $usersFaker;
    }



    // this route only for generate random users for testing
    #[Route('/randomusers', name:'generate-random-users')]
    public function generateRandomUsersAction(): Response{
        $this->usersFaker->createRandomUsers(10);
        return $this->redirect('Users');
    }



    #[Route('/Users', name: 'customers-list')]
    public function index(): Response
    {
        $customers = $this->usersRepository->findCustomerByRoles('ROLE_CUSTOMER');

        return $this->render('MyPages/Customers/customerList.html.twig', [
            'customers' => $customers,
        ]);
    }



    #[Route('/admin/Page', name: 'admins-list')]
    public function displayAdmiPage(): Response
    {
        return $this->render('MyPages/Users/adminsList.html.twig');
    }



    #[Route('/Users/customerslist', name: 'user-customer-list')]
    public function customerList(): JsonResponse
    {
        // Fetch all customers with the role 'ROLE_CUSTOMER'
        $customers = $this->usersRepository->findCustomerByRoles('ROLE_ADMIN');
        // $customers = $this->usersRepository->findAll();

        // Prepare the data array
        $data = [];
        foreach ($customers as $customer) {
            $data[] = [
                'id' => $customer->getId(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                'phone' => $customer->getPhoneNumber()
            ];
        }

        // Return the data as a JSON response
        return $this->json($data);
    }




    #[Route('/Users/delete/{customerId}', name: 'delete-customer')]
    public function deleteCustomer(int $customerId): JsonResponse
    {
        try {
            // Attempt to delete the customer
            $result = $this->usersRepository->deleteById($customerId);

            if ($result) {
                return new JsonResponse(
                    [
                        'status' => 'successRemoving',
                        'message' => 'Customer successfully removed.'
                    ],
                    Response::HTTP_OK
                );
            }

            return new JsonResponse(
                [
                    'status' => 'errorRemoving',
                    'message' => 'Customer not found or could not be removed.'
                ],
                Response::HTTP_NOT_FOUND
            );
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse(
                [
                    'status' => 'errorRemoving',
                    'message' => 'This customer cannot be deleted because it contains associated orders. Please remove or reassign the orders before deleting the customer.'
                ],
                Response::HTTP_CONFLICT
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'status' => 'errorRemoving',
                    'message' => 'An error occurred while trying to delete the customer.'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
