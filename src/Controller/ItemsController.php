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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ItemsController extends AbstractController
{
    private ItemsService $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }

    #[Route('/addItem', name: 'addItem')]
    public function addItem(Request $request): Response
    {
        $result = $this->itemsService->handleAddItem($request);

        if ($result) {
            $this->addFlash('addProduct', 'Product added successfully!');
            return $this->redirect('add-item-page');
        }
        $this->addFlash('addProductError', 'Failed to add product.');
        return new Response(null, Response::HTTP_NOT_FOUND);
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

    #[Route('/Products/{tag}', name: 'findByTag', methods: ['GET'])]
    public function findItemsByTag(string $tag): Response
    {

        $items = $this->itemsService->findItemsByTag($tag);

        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items,
        ]);
    }





    #[Route('/search-product-update', name: 'search-product-update', methods: ['GET'])]
    public function searchProudctForUpdate(Request $request, ItemsRepository $itemsRepository): Response
    {
        $name = $request->query->get('searchProduct');

        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        $items = $itemsRepository->findByPartialName($name);
        return $this->render('items/updateItemPage.html.twig', [
            'items' => $items,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/update-item-page', name: 'update-item-page')]
    public function updateItemPage(Request $request,): Response
    {

        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        $items = $this->itemsService->getAllProducts();
        return $this->render('items/updateItemPage.html.twig', [
            'items' => $items,
            'form' => $form->createView(),

        ]);
    }



    #[Route('/item/update/{id}', name: 'item_update')]
    public function update(Request $request, EntityManagerInterface $em)
    {

        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->$em->getManager()->flush();

            return $this->redirectToRoute('item_list');
        }

        $items = $this->itemsService->getAllProducts();
        return $this->render('items/updateItemPage.html.twig', [
            'form' => $form->createView(),
            'items' => $items
        ]);
    }

    #[Route('/updateProduct', name: 'updateProduct')]
    public function updateProduct(Request $request)
    {
        $result = $this->itemsService->handleUpdateProduct($request);
        if ($result) {
            $this->addFlash('updateProduct', 'Product updated successfully!');
            return $this->redirect('update-item-page');
        }
        $this->addFlash('updateProductError', 'Failed to update product.');
        return new Response(null, Response::HTTP_NOT_FOUND);
    }




}
