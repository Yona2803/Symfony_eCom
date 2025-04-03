<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentPageController extends AbstractController
{
    #[Route('/payment', name: 'payment_page')]
    public function index(): Response
    {
        return $this->render('MyPages/Payment/PaymentPage.html.twig');
    }



    #[Route('/cancel', name: 'payment_cancel')]
    public function cancelPayment () {
        return $this->redirectToRoute('CheckOut');
    }
}
