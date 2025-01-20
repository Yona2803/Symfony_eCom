<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;

class MyCartPageController extends AbstractController
{
    #[Route('/MyCart', name: 'MyCartPage')]
    public function cart()
    {
        return $this->render('Pages/CartPage/CartPage.html.twig');
    }

    #[Route('/MyCart/ShowProducts', name: 'ShowProducts', methods: ['GET'])]
    public function ShowProducts(Request $request, EntityManagerInterface $entityManager)
    {
        $itemsIds = $request->query->get('items_ids');
    
        if (!$itemsIds) {
            return new Response('no items in the URL', 200);
        }

        $decodedItems = json_decode($itemsIds, true);
        // Check if decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new Response('Invalid JSON format', 400);
        }

        // 2nd stage : getting items from db
        $products = $entityManager->getRepository(Items::class)->findBy(['id' => $decodedItems]);

        if (!$products) {
            return new Response('DB: no data');
        }

        // preparing the Array response
        $productDetails = [];

        foreach ($products as $product) {
            $itemImage = $product->getItemImage();
            if (is_resource($itemImage)) {
                $itemImage = stream_get_contents($itemImage);
            }
            $base64Image = base64_encode($itemImage);

            $productDetails[] = [
                [
                    'id' => $product->getId(),
                    'itemImage' => 'data:image/jpg;base64,' . $base64Image,
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'stock' => $product->getStock()
                ]
            ];
        }

        if (!$productDetails) {
            return new Response('Products : no data');
        }

        return new JsonResponse($productDetails);
    }
}
