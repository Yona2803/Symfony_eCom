<?php

namespace App\Service;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

class CategoriesService
{


    public function __construct(
        private EntityManagerInterface $em,
        private FormFactoryInterface $formFactory,
        private CategoriesRepository $categoriesRepository
    ){
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->categoriesRepository = $categoriesRepository;
    }



    public function handleAddCategory(Request $request): bool
    {
        $category = new Categories();
        $form = $this->formFactory->create(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('categoryImage')->getData();

            if ($imageFile) {
                $binaryData = file_get_contents($imageFile->getPathname());
                $category->setCategoryImage($binaryData);
            }
            $categoryName = $form->get('name')->getData();
            $category->setName($categoryName);

            $this->em->persist($category);
            $this->em->flush();
            
            return true;
        }

        return false;
    }


    public function getAllCategories(){
        return $this->categoriesRepository->findAll();
    }

}