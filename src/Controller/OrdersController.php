<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrdersController extends AbstractController
{



    function __construct(
        private OrdersRepository $ordersRepository
    ){
        $this->ordersRepository = $ordersRepository;
    }






    #[Route('/orders', name: 'orders-list')]
    public function index(): Response
    {

        $orders = $this->ordersRepository->findAll();

        return $this->render('items/orderPage.html.twig', [
            'orders' => $orders,
        ]);
    }
}
