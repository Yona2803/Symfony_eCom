<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use App\Service\OrdersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrdersController extends AbstractController
{



    function __construct(
        private OrdersRepository $ordersRepository,
        private OrdersService $ordersService
    ) {
        $this->ordersRepository = $ordersRepository;
    }




    #[Route('/orders', name: 'orders-list')]
    public function listOrders(Request $request, OrdersRepository $orderRepository): Response
    {
        $page = $request->query->getInt('page', 1); // Get the current page from the request
        $limit = OrdersRepository::PAGINATOR_PER_PAGE; // Results per page
        $offset = ($page - 1) * $limit; // Calculate the offset

        $paginator = $orderRepository->findOrderDetails($offset, $limit);
        $totalResults = count($paginator);
        $totalPages = ceil($totalResults / $limit);

        return $this->render('MyPages/Orders/orderPage.html.twig', [
            'orders' => $paginator,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }


    #[Route('/orders/returns', name: 'orders-returns')]
    public function ordersReturnsRequestList(Request $request, OrdersRepository $orderRepository): Response
    {
        $page = $request->query->getInt('page', 1); // Get the current page from the request
        $limit = OrdersRepository::PAGINATOR_PER_PAGE; // Results per page
        $offset = ($page - 1) * $limit; // Calculate the offset

        $paginator = $orderRepository->findOrderDetails($offset, $limit);
        $totalResults = count($paginator);
        $totalPages = ceil($totalResults / $limit);

        return $this->render('MyPages/Orders/ordersReturnsRequestPage.html.twig', [
            'orders' => $paginator,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }



    #[Route('/order/{orderId}/{orderStatus}', name: 'change-order-status')]
    public function changeOrderStatus(int $orderId, string $orderStatus): JsonResponse
    {
        $newOrderStatus = $this->ordersService->changeOrderStatus($orderId, $orderStatus);
        return new JsonResponse([
            'status' => 'successChanged',
            'message' => 'Order status successfully changed.',
            'orderStatus' => $newOrderStatus
        ], Response::HTTP_OK);
    }


    #[Route('/orderDetails/{orderId}', name: 'order-details')]
    public function getOrderDetailsByOrderId(int $orderId): JsonResponse
    {
        $orderDetails = $this->ordersService->getOrderDetailsByOrderId($orderId);
        return new JsonResponse([
            'status' => 'success',
            'orderDetails' => $orderDetails
        ], Response::HTTP_OK);
    }
}
