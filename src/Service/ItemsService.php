<?php

namespace App\Service;

use App\Entity\Categories;
use App\Entity\Items;
use App\Form\ItemType;
use App\Repository\ItemsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

class ItemsService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface $formFactory,
        private ItemsRepository $itemsRepository
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->itemsRepository = $itemsRepository;
    }




    public function handleAddItem(Request $request): bool
    {
        $item = new Items();
        $form = $this->formFactory->create(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $selectedTags = $form->get('tags')->getData();
            $item->setTags($selectedTags);


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
                $this->entityManager->persist($categoryObj);
                $this->entityManager->flush();
            }
            $item->setCategory($categoryObj);

            $price = $form->get('price')->getData();
            $item->setPrice((float)$price);

            $this->entityManager->persist($item);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }






    /**
     * @return Items[]
     */
    public function getAllProducts(): array
    {
        return $this->itemsRepository->findAll();
    }


    public function getProductByName(string $name): array
    {
        return $this->itemsRepository->findByPartialName($name);
    }



    public function findItemsByTag(string $tag): array
    {
        if (!$tag) {
            return [];
        }

        return $this->itemsRepository->findByTag($tag);
    }





    public function handleUpdateProduct(Request $request): bool
    {
        $productId = $request->request->get('productId'); 
        $item = $this->itemsRepository->find($productId);
        $form = $this->formFactory->create(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $selectedTags = $form->get('tags')->getData();
            $item->setTags($selectedTags);


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
                $this->entityManager->persist($categoryObj);
                $this->entityManager->flush();
            }
            $item->setCategory($categoryObj);

            $price = $form->get('price')->getData();
            $item->setPrice((float)$price);

            $this->entityManager->persist($item);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }


}
