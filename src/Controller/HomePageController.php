<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'homePage')]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $items = $entityManager->getRepository(Items::class)->findAll();
        return $this->render('base.html.twig', [
            'items' => $items
        ]);
    }
}
