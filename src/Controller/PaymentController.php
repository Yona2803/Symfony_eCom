<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/api/payment/checkout', name: 'stripe_checkout', methods: ['POST'])]
    public function checkout(Request $request): JsonResponse
    {
        // Decode JSON request body
        $data = json_decode($request->getContent(), true);
        $amount = isset($data['amount']) ? (int) $data['amount'] : 500; // Default to 500 cents (5 USD)
        $email = isset($data['email']) ? (string) $data['email'] : '';
        $fullName = isset($data['fullName']) ? (string) $data['fullName'] : '';

        if ($amount <= 0) {
            return new JsonResponse(['error' => 'Invalid amount'], 400);
        }

        // Set Stripe API key
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            // Create Stripe Checkout Session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => ['name' => 'Sample Product'],
                        'unit_amount' => $amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => $email,  // Pass the email to Stripe
                'metadata' => [
                    'cardholder_name' => $fullName,  // Pass the name to Stripe
                ],
                'success_url' => 'http://localhost:8000/success',
                'cancel_url' => 'http://localhost:8000/cancel',
            ]);

            return new JsonResponse(['url' => $session->url]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
