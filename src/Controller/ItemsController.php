<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;
use App\Faker\ProductsFaker;
use App\Form\ItemType;
use App\Repository\ItemsRepository;
use App\Service\ItemsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\MimeTypes;

class ItemsController extends AbstractController
{

    public function __construct(
        private ItemsService $itemsService,
        private ItemsRepository $itemsRepository,
        private ProductsFaker $productsFaker
    ) {
        $this->itemsService = $itemsService;
        $this->itemsRepository = $itemsRepository;
        $this->productsFaker = $productsFaker;
    }


    #[Route('/randomitems', name: 'generate-random-items')]
    public function generateRandomItemsAction()
    {
        $this->productsFaker->createRandomItems(10);
        return $this->redirect('productsPage');
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

    // #[Route('/productsPage', name: 'productsPage')]
    // public function products(): Response
    // {
    //     $items = $this->itemsService->getAllProducts();
    //     return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
    //         'items' => $items
    //     ]);
    // }



    #[Route('/productsPage', name: 'productsPage')]
    public function products(Request $request, ItemsRepository $itemsRepository): Response
    {
        $page = $request->query->getInt('page', 1); // Get the current page from the request
        $limit = ItemsRepository::PAGINATOR_PER_PAGE; // Results per page
        $offset = ($page - 1) * $limit; // Calculate the offset

        $paginator = $itemsRepository->findProducts($offset, $limit);
        $totalResults = count($paginator);
        $totalPages = ceil($totalResults / $limit);

        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $paginator,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }









    #[Route('/Products', name: 'search', methods: ['GET'])]
    public function search(Request $request, ItemsRepository $itemsRepository): Response
    {
        $name = $request->query->get('searchInput');

        $page = $request->query->getInt('page', 1); // Get the current page from the request
        $limit = ItemsRepository::PAGINATOR_PER_PAGE; // Results per page
        $offset = ($page - 1) * $limit; // Calculate the offset

        $paginator = $itemsRepository->findByPartialName($name, $offset, $limit);
        $totalResults = count($paginator);
        $totalPages = ceil($totalResults / $limit);

        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $paginator,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }


    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function toDashboard(): Response
    {
        return $this->render('MyPages/Dashboard/dashBoard.html.twig');
    }

    #[Route('/add-item-page', name: 'add_item_page')]
    public function addItemPage(): Response
    {
        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);

        return $this->render('MyPages/Products/addItemPage.html.twig', [
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

        $page = $request->query->getInt('page', 1); 
        $limit = ItemsRepository::PAGINATOR_PER_PAGE; 
        $offset = ($page - 1) * $limit; 

        $paginator = $itemsRepository->findByPartialName($name, $offset, $limit);
        $totalResults = count($paginator);
        $totalPages = ceil($totalResults / $limit);

        return $this->render('MyPages/Products/updateItemPage.html.twig', [
            'form' => $form->createView(),
            'items' => $paginator,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }



    #[Route('/update-item-page', name: 'update-item-page')]
    public function updateItemPage(Request $request,): Response
    {
        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        // $items = $this->itemsService->getAllProducts();
        // return $this->render('MyPages/Products/updateItemPage.html.twig', [
        //     'items' => $items,
        //     'form' => $form->createView(),

        // ]);

        $page = $request->query->getInt('page', 1); // Get the current page from the request
        $limit = ItemsRepository::PAGINATOR_PER_PAGE; // Results per page
        $offset = ($page - 1) * $limit; // Calculate the offset

        $paginator = $this->itemsService->getAllProductsWithPagination($offset, $limit);
        $totalResults = count($paginator);
        $totalPages = ceil($totalResults / $limit);

        return $this->render('MyPages/Products/updateItemPage.html.twig', [
            'form' => $form->createView(),
            'items' => $paginator,
            'totalPages' => $totalPages,
            'currentPage' => $page,
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

        // $items = $this->itemsService->getAllProducts();
        // return $this->render('MyPages/Products/updateItemPage.html.twig', [
        //     'form' => $form->createView(),
        //     'items' => $items
        // ]);

        $page = $request->query->getInt('page', 1); // Get the current page from the request
        $limit = ItemsRepository::PAGINATOR_PER_PAGE; // Results per page
        $offset = ($page - 1) * $limit; // Calculate the offset

        $paginator = $this->itemsService->getAllProductsWithPagination($offset, $limit);
        $totalResults = count($paginator);
        $totalPages = ceil($totalResults / $limit);

        return $this->render('MyPages/Products/updateItemPage.html.twig', [
            'form' => $form->createView(),
            'items' => $paginator,
            'totalPages' => $totalPages,
            'currentPage' => $page,
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


    #[Route('/products/{productId}', name: 'find-product', methods: ['GET'])]
    public function findProductById(int $productId): JsonResponse
    {
        $item = $this->itemsRepository->findOneBy(['id' => $productId]);

        if (!$item) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        if ($item->getItemImage()) {
            $imageData = base64_encode(stream_get_contents($item->getItemImage()));
        } else {
            $imageData = null;
        }

        return new JsonResponse([
            'name' => $item->getName(),
            'price' => $item->getPrice(),
            'stock' => $item->getStock(),
            'description' => $item->getDescription(),
            'tags' => $item->getTags(),
            'category' => $item->getCategory()->getId(),
            'image' => $imageData,
        ]);
    }
}
