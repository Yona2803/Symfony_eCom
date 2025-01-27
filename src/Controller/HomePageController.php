<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;
use App\Service\ItemsService;

class HomePageController extends AbstractController
{

    private ItemsService $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }


    
    #[Route('/home', name: 'homePage')]
    public function getAll(): Response
    {
        $items = $this->itemsService->getAllProducts();
        return $this->render('/base.html.twig', [
            'items' => $items
        ]);
    }


    #[Route('/', name:'home')]
    public function redirectToHomePage(){
        return $this->redirectToRoute('homePage');
    }


}
