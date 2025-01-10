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

class ItemsController extends AbstractController
{

    #[Route('/addItemPage', name: 'addItemPage')]
    public function addItemPage()
    {
        return $this->render('items/addItemPage.html.twig');
    }



    #[Route('/addItem', name: 'addItem')]
    public function addItem(Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new Items();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('itemImage')->getData();

            if ($imageFile) {
                $binaryData = file_get_contents($imageFile->getPathname());
                $item->setItemImage($binaryData);
            }

            $categoryName = $form->get('newCategory')->getData();
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

        return $this->render('items/addItemPage.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/listItems', name: 'listItems')]
    public function getAll(Request $request, EntityManagerInterface $entityManager): Response
    {
        $items = $entityManager->getRepository(Items::class)->findAll();
        return $this->render('/base.html.twig', [
            'items' => $items
        ]);
    }
}
