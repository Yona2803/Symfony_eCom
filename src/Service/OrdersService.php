<?php

namespace App\Service;

use App\Repository\OrderDetailsRepository;
use App\Repository\OrdersRepository;
use App\Entity\OrderState;
use App\Repository\OrderStatusRepository;
use App\Repository\OrderStateRepository;
use App\Repository\StateRepository;
use App\Repository\StateStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;

class OrdersService
{
    public function __construct(
        private OrdersRepository $ordersRepository,
        private OrderDetailsRepository $orderDetailsRepository,
        private OrderStatusRepository $orderStatusRepository,
        private OrderStateRepository $orderStateRepository,
        private StateRepository $StateRepository,
        private StateStatusRepository $StateStatusRepository,
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private MessageBusInterface $bus,
        private EmailSenderService $emailSenderService,
    ) {
        $this->ordersRepository = $ordersRepository;
        $this->orderDetailsRepository = $orderDetailsRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStateRepository = $orderStateRepository;
        $this->StateRepository = $StateRepository;
        $this->StateStatusRepository = $StateStatusRepository;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->bus = $bus;
        $this->emailSenderService = $emailSenderService;
    }



    public function changeOrderStatusState($orderId, $state, $emailCustomer): bool
    {
        $order = $this->ordersRepository->findOneBy(['id' => $orderId]);
        $orderState = $this->orderStateRepository->findOneBy(['Order' => $order]);
        $orderStateValue = $orderState->getState()->getName();

        $newState = $this->StateStatusRepository->findOneBy(['name' => $state]);

        $newValue = $order->getOrderStatus()->getStatusName();

        if ($order) {
            if ($state == 'Accepted') {
                $newValue = $orderStateValue == 'Cancel' ? 'Cancelled' : 'Returned';
                $statusName = $this->orderStatusRepository->findOneBy(['statusName' => $newValue]);
                $order->setOrderStatus($statusName);
            }


            $orderState->setStateStatus($newState);
            $this->em->flush();
    
            // Prepare email parameters
            $templateParams = [
                'customerName' => $order->getUser()->getFirstName(),
                'orderId' => $order->getId(),
                'orderStateStatus' => $state, // accepted or declined
                'orderState' => $orderStateValue, // cancel or return
                'orderStatus' => $newValue, // Cancelled or returned
            ];

            $subject = (string) $orderStateValue.' '.'Request';
    
            // Send the email using the service
            $this->emailSenderService->sendCancellationEmail($emailCustomer, $subject, $templateParams);
    
            return true;
        }

        return false;
    }
    public function changeOrderStatusV2($orderId, $orderStatus): bool
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
            } else {
                return false;
            }
        }

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
