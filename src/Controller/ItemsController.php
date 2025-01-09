<?php

namespace App\Controller;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;

class ItemsController extends AbstractController
{
    #[Route('/addItemPage', name: 'appItemPage')]
    public function addItemPage()
    {
        return $this->render('items/addItemPage.html.twig');
    }



    #[Route('/addItem', name: 'addItem', methods: ['POST'])]
    public function addItem(Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($request->isMethod('POST')) {

            $name = $request->request->get('name');
            $price = $request->request->get('price');
            $stock = $request->request->get('stock');
            $description = $request->request->get('description');
            $categoryName = $request->request->get('category');


            $item = new Items();
            $item->setName($name);
            $item->setPrice((float)$price);
            $item->setStock((int)$stock);
            $item->setDescription((string) $description);

            $categoryObj = $entityManager->getRepository(Categories::class)->findOneBy(['name' => $categoryName]);
            if ($categoryObj === null) {
                $categoryObj = new Categories();
                $categoryObj->setName($categoryName);
                
                $entityManager->persist($categoryObj);
                $entityManager->flush();
            } else {
                $item->setCategory($categoryObj);

                $entityManager->persist($item);
                $entityManager->flush();
            }


            return $this->render('items/addItemPage.html.twig');
        }
        return $this->render('items/addItemPage.html.twig');
    }
}
