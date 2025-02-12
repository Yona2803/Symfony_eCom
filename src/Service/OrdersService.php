<?php

namespace App\Service;

use App\Repository\OrderDetailsRepository;
use App\Repository\OrdersRepository;
use App\Repository\OrderStatusRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrdersService
{

    function __construct(
        private OrdersRepository $ordersRepository,
        private OrderDetailsRepository $orderDetailsRepository,
        private OrderStatusRepository $orderStatusRepository,
        private EntityManagerInterface $em
    ){
        $this->ordersRepository = $ordersRepository;
        $this->orderDetailsRepository = $orderDetailsRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->em = $em;
    }



    public function changeOrderStatus($orderId, $orderStatus): string {

        $order = $this->ordersRepository->findOneBy(['id' => $orderId]);
        $orderStatus = $this->orderStatusRepository->findOneBy(['statusName' => $orderStatus]);
        $order->setOrderStatus($orderStatus);

        $this->em->persist($order);
        $this->em->flush();
        
        return $orderStatus->getStatusName();
    }



    public function getOrderDetailsByOrderId($orderId){
        return $this->orderDetailsRepository->getOrderDetailsByOrderId($orderId);
    }



}
