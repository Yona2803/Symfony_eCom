<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    
    #[Route('/MyAccount', name: 'MyAccountPage')]
    public function cart()
    {
        return $this->render('Pages/AccountPage/AccountPage.html.twig');
    }
}
