<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;
use App\Service\CategoriesService;
use App\Service\ItemsService;

class HomePageController extends AbstractController
{

    public function __construct(
        private ItemsService $itemsService,
        private CategoriesService $categoriesService
        )
    {
        $this->itemsService = $itemsService;
        $this->categoriesService = $categoriesService;
    }



    #[Route('/home', name: 'home')]
    public function getAll(): Response
    {
        $items = $this->itemsService->getAllProducts();
        $categories = $this->categoriesService->getAllCategories();
        return $this->render('/base.html.twig', [
            'items' => $items,
            'categories' => $categories
        ]);
    }


    #[Route('/', name: 'homePage')]
    public function redirectToHomePage()
    {
        return $this->redirectToRoute('home');
    }
}
