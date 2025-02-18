<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use App\Repository\ItemsRepository;
use App\Service\CategoriesService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoriesController extends AbstractController
{

    public function __construct(
        private ItemsRepository $itemsRepository,
        private CategoriesService $categoriesService,
        private CategoriesRepository $categoriesRepository
    ) {
        $this->itemsRepository = $itemsRepository;
        $this->categoriesService = $categoriesService;
        $this->categoriesRepository = $categoriesRepository;
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
        return $this->render('MyPages/Categories/addCategoryPage.html.twig', [
            'form' => $form->createView()
        ]);
    }



    #[Route('/Category/{categoryName}', name: 'searchByCategory', methods: ['GET'])]
    public function searchByCategory(string $categoryName): Response
    {
        $items = $this->itemsRepository->findByCategoryName($categoryName);
        return $this->render('Pages/ProductsPage/ProductsPage.html.twig', [
            'items' => $items,
        ]);
    }


    #[Route('/categories-list', name: 'categories_list')]
    public function listCategories(): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $categories = $this->categoriesService->getAllCategories();

        $categoriesData = array_map(function ($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'image' => $category->getCategoryImage() ? base64_encode(stream_get_contents($category->getCategoryImage())) : null,
            ];
        }, $categories);

        return $this->render('MyPages/Categories/CategoriesPage.html.twig', [
            'categories' => $categoriesData,
            'form' => $form->createView()
        ]);
    }



    #[Route('/categories/delete/{categoryId}', name: 'delete-categories')]
    public function deleteCategory(int $categoryId): JsonResponse
    {
        try {
            // Attempt to delete the customer
            $result = $this->categoriesService->deleteCategoryById($categoryId);

            if ($result) {
            return new JsonResponse(
                [
                    'status' => 'successRemoving',
                    'message' => 'Category successfully removed.'
                ],
                Response::HTTP_OK
            );
        }
        return new JsonResponse(
            [
                'status' => 'failingRemoving',
                'message' => 'Category failed removed.'
            ],
            Response::HTTP_OK
        );
        
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse(
                [
                    'status' => 'errorRemoving',
                    'message' => 'This category cannot be deleted because it contains associated items. Please remove or reassign the items before deleting the category.'
                ],
                Response::HTTP_CONFLICT
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'status' => 'errorRemoving',
                    'message' => 'An error occurred while trying to delete the category.'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR 
            );
        }
    }


    #[Route('/update-category', name: 'update-category')]
    public function updateCatergory(Request $request)
    {
        try {
            $result = $this->categoriesService->handleUpdateCategory($request);

            if ($result) {
                $this->addFlash('updateCategory', 'Category updated successfully!');
                return $this->redirect('categories-list');
            }
            $this->addFlash('updateCategoryError', 'Failed to update category.');
        } catch (UniqueConstraintViolationException $e) {
            $this->addFlash('duplicateName', 'This category name already exists.');
        }

        return $this->redirect('categories-list');
    }





    #[Route('/categories/{categoryId}', name: 'find-category', methods: ['GET'])]
    public function getCategoryById(int $categoryId): JsonResponse
    {
        $category = $this->categoriesRepository->findOneBy(['id' => $categoryId]);

        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], 404);
        }

        if ($category->getCategoryImage()) {
            $imageData = base64_encode(stream_get_contents($category->getCategoryImage()));
        }
        
        return new JsonResponse([
            'name' => $category->getName(),
            'image' => $imageData,
        ]);
                
    }
}
