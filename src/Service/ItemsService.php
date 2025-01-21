<?php

namespace App\Service;

use App\Entity\Categories;
use App\Entity\Items;
use App\Form\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

class ItemsService
{
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
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
        return $this->entityManager->getRepository(Items::class)->findAll();
    }


    public function getProductByName(string $name): array
    {
        return $this->entityManager->getRepository(Items::class)->findByPartialName($name);
    }



    public function findItemsByTag(string $tag): array
    {
        $tag = null;
        if (!$tag) {
            return [];
        }

        return $this->entityManager->getRepository(Items::class)->findByTag($tag);
    }



}
