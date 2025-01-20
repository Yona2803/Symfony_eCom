<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;
use App\Form\ItemType;
use App\Repository\ItemsRepository;
use App\Service\ItemsService;


class ItemsController extends AbstractController
{
    private ItemsService $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }


    #[Route('/addItem', name: 'addItem')]
    public function addItem2(Request $request): Response
    {
        $result = $this->itemsService->handleAddItem($request);

        if ($result) {
            $this->addFlash('addProduct', 'Product added successfully!');
            return $this->redirect('add-item-page');
        }
        $this->addFlash('addProductError', 'Failed to add product.');
        return new Response(null, Response::HTTP_NOT_FOUND);
    }



    #[Route('/', name: 'homePage')]
    #[Route('/home', name: 'home')]
    public function getAll(): Response
    {
        $items = $this->itemsService->getAllProducts();
        return $this->render('/base.html.twig', [
            'items' => $items
        ]);
    }



    #[Route('/productsPage', name: 'productsPage')]
    public function products(): Response
    {
        $items = $this->itemsService->getAllProducts();
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }



    #[Route('/Products', name: 'search', methods: ['GET'])]
    public function search(Request $request, ItemsRepository $itemsRepository): Response
    {
        $name = $request->query->get('searchInput');

        $items = $itemsRepository->findByPartialName($name);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }



    #[Route('/Category/{categoryName}', name: 'searchByCategory', methods: ['GET'])]
    public function searchByCategory(string $categoryName, ItemsRepository $itemsRepository): Response
    {
        $items = $itemsRepository->findByCategoryName($categoryName);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }



    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function toDashboard(): Response
    {
        return $this->render('items/dashBoard.html.twig');
    }



    #[Route('/add-item-page', name: 'add_item_page')]
    public function addItemPage(): Response
    {
        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);

        return $this->render('items/addItemPage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
