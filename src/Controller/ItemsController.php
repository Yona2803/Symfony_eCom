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

    #[Route('/addItemPage', name: 'addItemPage')]
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
            $imageFile = $request->files->get('image');

            $item = new Items();
            $item->setName($name);
            $item->setPrice((float) $price);
            $item->setStock((int) $stock);
            $item->setDescription((string) $description);


            if ($imageFile) {
                if ($imageFile->isValid()) {
                    $imageData = file_get_contents($imageFile->getPathname());
                    $item->setItemImage($imageData);
                } else {
                    return $this->redirectToRoute('addItemPage');
                }
            } else {
                return $this->redirectToRoute('addItemPage');
            }


            $categoryObj = $entityManager->getRepository(Categories::class)->findOneBy(['name' => $categoryName]);
            if ($categoryObj === null) {
                $categoryObj = new Categories();
                $categoryObj->setName($categoryName);
                $entityManager->persist($categoryObj);
                $entityManager->flush();
            }
            $item->setCategory($categoryObj);


            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute('listItems');
        }

        return $this->redirectToRoute('addItemPage');
    }



    #[Route('/listItems', name: 'listItems')]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $items = $entityManager->getRepository(Items::class)->findAll();
        return $this->render('items/listItems.html.twig', [
            'items' => $items
        ]);
    }

}
