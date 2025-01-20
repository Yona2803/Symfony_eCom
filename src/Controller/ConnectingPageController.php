<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ConnectingPageController extends AbstractController
{
    #[Route('/Connecting', name: 'ConnectingPage')]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('Pages/ConnectingPage/ConnectingPage.html.twig');
    }
}
