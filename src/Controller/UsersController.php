<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{

    public function __construct(
        private UsersRepository $usersRepository
    ) {
        $this->usersRepository = $usersRepository;
    }




    #[Route('/Users/customers', name: 'customers-list')]
    public function index(): Response
    {
        $customers = $this->usersRepository->findCustomerByRoles('ROLE_CUSTOMER');

        return $this->render('items/customerList.html.twig', [
            'customers' => $customers,
        ]);
    }




    #[Route('/Users/delete/{customerId}', name: 'delete-customer')]
    public function deleteCustomer(int $customerId): JsonResponse
    {

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
    }



}
