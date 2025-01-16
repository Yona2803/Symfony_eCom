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

class ItemsController extends AbstractController
{

    private ItemsService $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }


    #[Route('/addItem', name: 'addItem')]
    public function addItem2(Request $request): Response
    {
        $result = $this->itemsService->handleAddItem($request);

        if ($result['success']) {
            return $this->redirectToRoute('home');
        }

        return $this->render('items/addItemPage.html.twig', [
            'form' => $result['form']->createView(),
        ]);
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): Response
    {
        return $this->render('items/dashBoard.html.twig');
    }
}
