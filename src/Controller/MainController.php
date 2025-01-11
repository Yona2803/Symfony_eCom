<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'homePage')]
    public function index()
    {
        return $this->render('base.html.twig');
    }
<<<<<<< Updated upstream
=======

    #[Route('/MyCart', name: 'MyCartPage')]
    public function cart()
    {
        return $this->render('Pages/CartPage/CartPage.html.twig');
    }
>>>>>>> Stashed changes
}
