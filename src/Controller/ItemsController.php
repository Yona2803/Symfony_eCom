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
use Symfony\Component\HttpFoundation\JsonResponse;

class ItemsController extends AbstractController
{

    private ItemsService $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }


    #[Route('/addItem', name: 'addItem')]
    public function addItem(Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $imageFile = $form->get('itemImage')->getData();

            if ($imageFile) {
                $binaryData = file_get_contents($imageFile->getPathname());
                $item->setItemImage($binaryData);
            }

            $categoryObj = $form->get('category')->getData();
            if ($categoryObj === null) {
                $categoryName = $form->get('category')->getViewData();
                $categoryObj = new Categories();
                $categoryObj->setName($categoryName);
                $entityManager->persist($categoryObj);
                $entityManager->flush();
            }
            $item->setCategory($categoryObj);

            $price = $form->get('price')->getData();
            $item->setPrice((float)$price);

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('items/addItemPage.html.twig', [
            'form' => $form->createView(),
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


}
