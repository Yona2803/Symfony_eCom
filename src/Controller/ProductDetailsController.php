<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;
use SebastianBergmann\Environment\Console;

class ProductDetailsController extends AbstractController
{
    // Controller
    #[Route('/ProductDetails/{id}', name: 'ProductDetails')]
    public function ProductDetails(int $id, EntityManagerInterface $entityManager): Response
    {
        // Now you can use $id to fetch the specific product
        $product = $entityManager->getRepository(Items::class)->find($id);

        // Check if product exists
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
    

        return $this->render('Pages/ProductsPage/ProductDetails/ProductDetails.html.twig', [
            'ProductDetails' => $product,
            // 'Back_ToPage' => $PageInfo
        ]);
    
    }
}
