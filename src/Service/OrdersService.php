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



    public function changeOrderStatus($orderId, $orderStatus): bool 
    {

        $order = $this->ordersRepository->findOneBy(['id' => $orderId]);

        if ($orderStatus == 'Delivered') {
            $orderStatus = $this->orderStatusRepository->findOneBy(['statusName' => $orderStatus]);
            $order->setOrderStatus($orderStatus);

            $this->em->persist($order);
            $this->em->flush();
        } else {
            $State = ($orderStatus === 'Cancelled') ? 'Cancel' : 'Return';

            // Check if an OrderState already exists for this orderId
            $existingOrderState = $this->orderStateRepository->findOneBy(['Order' => $orderId]);

            if (!$existingOrderState) {
                $this->SyncOrderState($orderId, $State, 'Pending');
            }else{
                return false;
            }
        }

        return true;
        // return $orderStatus->getStatusName();
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
