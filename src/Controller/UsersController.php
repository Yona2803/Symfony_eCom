<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
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




    #[Route('/Users', name: 'customers-list')]
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
