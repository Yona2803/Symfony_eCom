<?php

namespace App\Service;

use App\Repository\OrderDetailsRepository;
use App\Repository\OrdersRepository;
use App\Entity\OrderState;
use App\Repository\OrderStatusRepository;
use App\Repository\OrderStateRepository;
use App\Repository\StateRepository;
use App\Repository\StateStatusRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Request;

class OrdersService
{

    public function __construct(
        private OrdersRepository $ordersRepository,
        private OrderDetailsRepository $orderDetailsRepository,
        private OrderStatusRepository $orderStatusRepository,
        private OrderStateRepository $orderStateRepository,
        private StateRepository $StateRepository,
        private StateStatusRepository $StateStatusRepository,
        private EntityManagerInterface $em
    ) {
        $this->ordersRepository = $ordersRepository;
        $this->orderDetailsRepository = $orderDetailsRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStateRepository = $orderStateRepository;
        $this->StateRepository = $StateRepository;
        $this->StateStatusRepository = $StateStatusRepository;
        $this->em = $em;
    }



    public function changeOrderStatusState($orderId, $state): bool
    {
        $order = $this->ordersRepository->findOneBy(['id' => $orderId]);
        $orderState = $this->orderStateRepository->findOneBy(['Order' => $order]);
        $value = $orderState->getState()->getName();

        $newState = $this->StateStatusRepository->findOneBy(['name' => $state]);

        if ($order) {
            if ($state == 'Accepted') {
                if ($value == 'Cancel') {
                    $newValue = 'Cancelled';
                } else {
                    $newValue = 'Returned';
                }
                $statusName = $this->orderStatusRepository->findOneBy(['statusName' => $newValue]);
                $order->setOrderStatus($statusName);
            }


            $orderState->setStateStatus($newState);
            $this->em->persist($orderState);
            $this->em->flush();
            return true;
        }

        return false;
    }





    public function changeOrderStatus($orderId, $orderStatus): bool
    {
        // Retrieve the order
        $order = $this->ordersRepository->find($orderId);
        if (!$order) {
            return false; // Order not found
        }

        // Retrieve the order status
        $statusEntity = $this->orderStatusRepository->findOneBy(['statusName' => $orderStatus]);
        if (!$statusEntity) {
            return false; // Status not found
        }

        // Update order status and persist changes
        $order->setOrderStatus($statusEntity);
        $this->em->flush();

        return true;
    }


    private function SyncOrderState($orderId, $State, $StateStatus): OrderState
    {
        // Fetch the Orders entity using the orderId
        $order = $this->ordersRepository->find($orderId);

        if (!$order) {
            throw new \Exception('Order not found.');
        }

        // Create a new OrderState entity
        $orderState = new OrderState();
        $orderState->setOrder($order);

        // Fetch State and StateStatus entities
        $stateEntity = $this->StateRepository->findOneBy(['name' => $State]); // Directly use $State
        $stateStatusEntity = $this->StateStatusRepository->findOneBy(['name' => $StateStatus]); // Directly use $StateStatus

        if (!$stateEntity || !$stateStatusEntity) {
            throw new \Exception('State or StateStatus not found.');
        }

        // Set State and StateStatus
        $orderState->setState($stateEntity);
        $orderState->setStateStatus($stateStatusEntity);

        // Persist and flush the new OrderState
        $this->em->persist($orderState);
        $this->em->flush();

        return $orderState;
    }

    public function getOrderDetailsByOrderId($orderId)
    {
        return $this->orderDetailsRepository->getOrderDetailsByOrderId($orderId);
    }
}
