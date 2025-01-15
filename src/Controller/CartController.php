<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    
    #[Route('/MyCart', name: 'MyCartPage')]
    public function cart()
    {
        return $this->render('Pages/CartPage/CartPage.html.twig');
    }
}
