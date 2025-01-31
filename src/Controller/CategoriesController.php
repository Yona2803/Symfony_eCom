<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\ItemsRepository;
use App\Service\CategoriesService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoriesController extends AbstractController
{

    public function __construct(
        private ItemsRepository $itemsRepository,
        private CategoriesService $categoriesService
    ) {
        $this->itemsRepository = $itemsRepository;
        $this->categoriesService = $categoriesService;
    }


    #[Route('/add-category', name: 'add-category', methods: ['POST'])]
    public function addCatergory(Request $request)
    {

        try {
            $result = $this->categoriesService->handleAddCategory($request);
            if ($result) {
                $this->addFlash('addCategory', 'Category added successfully!');
                return $this->redirect('addCategoryPage');
            }
            $this->addFlash('addCategoryError', 'Failed to add category.');

        } catch (UniqueConstraintViolationException $e) {
            $this->addFlash('duplicateName', 'This category name already exists.');
        }

        return $this->redirect('addCategoryPage');
    }



    #[Route('/addCategoryPage', name: 'add-category-page')]
    public function getAddCategoryPage(): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        return $this->render('items/addCategoryPage.html.twig', [
            'form' => $form->createView()
        ]);
    }



    #[Route('/Category/{categoryName}', name: 'searchByCategory', methods: ['GET'])]
    public function searchByCategory(string $categoryName): Response
    {
        $items = $this->itemsRepository->findByCategoryName($categoryName);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items
        ]);
    }
}
