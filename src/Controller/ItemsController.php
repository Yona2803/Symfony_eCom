<?php

namespace App\Controller;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
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

        if ($result['success']) {
            return $this->redirectToRoute('home');
        }

        return $this->render('items/addItemPage.html.twig', [
            'form' => $result['form']->createView(),
        ]);
    }



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



    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, ItemsRepository $itemsRepository): Response
    {
        $name = $request->query->get('searchInput');

        $items = $itemsRepository->findByPartialName($name);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }



    #[Route('/ByCategory/{categoryName}', name: 'searchByCategory', methods: ['GET'])]
    public function searchByCategory(string $categoryName, ItemsRepository $itemsRepository): Response
    {
        $items = $itemsRepository->findByCategoryName($categoryName);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }


    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): Response
    {
        return $this->render('items/dashBoard.html.twig');
    }


    #[Route('/add-item-page', name: 'add_item_page')]
    public function addItemPage(Request $request): Response
    {
        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);

        return $this->render('items/addItemPageContent.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
