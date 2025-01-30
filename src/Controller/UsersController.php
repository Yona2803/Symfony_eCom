<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{

    public function __construct(
        private UsersRepository $usersRepository
    ){
        $this->usersRepository = $usersRepository;
    }




    #[Route('/customers', name: 'customers-list')]
    public function index(): Response
    {
        $customers = $this->usersRepository->findAll();

        return $this->render('items/customerList.html.twig', [
            'customers' => $customers,
        ]);
    }


    
}
