<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CategoriesService;
use App\Service\ItemsService;

class HomePageController extends AbstractController
{
    public function __construct(
        private ItemsService $itemsService,
        private CategoriesService $categoriesService
    ) {
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
