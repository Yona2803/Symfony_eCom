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
use App\Form\ItemType;
use App\Repository\ItemsRepository;

class ProductsPageController extends AbstractController
{
    private ItemsService $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }

    // Search - Logic
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, ItemsRepository $itemsRepository): Response
    {
        $name = $request->query->get('searchInput');

        $items = $itemsRepository->findByPartialName($name);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }

    // Search By Category - Logic
    #[Route('/ByCategory/{categoryName}', name: 'searchByCategory', methods: ['GET'])]
    public function searchByCategory(string $categoryName, ItemsRepository $itemsRepository): Response
    {
        $items = $itemsRepository->findByCategoryName($categoryName);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }

    // Products Controller
    #[Route('/Products', name: 'productsPage')]
    public function products(): Response
    {
        $items = $this->itemsService->getAllProducts();
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }
}
