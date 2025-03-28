<?php


namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\OrdersRepository;
use App\Repository\UsersRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/pdf')]
class PdfController extends AbstractController
{


    public function __construct(
        private OrdersRepository $ordersRepository,
        private UsersRepository $usersRepository,
        private CategoriesRepository $categoriesRepository
    ) {
        $this->ordersRepository = $ordersRepository;
        $this->usersRepository = $usersRepository;
        $this->categoriesRepository = $categoriesRepository;
    }




    #[Route('/orders', name: 'orders_pdf')]
    public function ordersListPdf(): Response
    {
        // Configure Dompdf options
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf
        $dompdf = new Dompdf($pdfOptions);

        $orderDetails = $this->ordersRepository->findOrderDetailsWithoutPagination();

        $Logo = base64_encode(file_get_contents(getcwd() . '/img/Logo/Logo.png'));
        $facebookLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/FaceBook.png'));
        $instagramLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/Instagram.png'));

        $html = $this->renderView('Pdf/ordersListPdf.html.twig', [
            'orderDetails' => $orderDetails,
            'logo' => $Logo,
            'facebook_logo' => $facebookLogo,
            'instagram_logo' => $instagramLogo
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        return new Response(
            $pdfOutput,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="orders list.pdf"',
            ]
        );
    }




    #[Route('/customers', name: 'customers_pdf')]
    public function customerListPdf(): Response
    {
        // Configure Dompdf options
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf
        $dompdf = new Dompdf($pdfOptions);

        $customerDetails = $this->usersRepository->findAllUsersByRole('ROLE_CUSTOMER');

        $Logo = base64_encode(file_get_contents(getcwd() . '/img/Logo/Logo.png'));
        $facebookLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/FaceBook.png'));
        $instagramLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/Instagram.png'));


        $html = $this->renderView('Pdf/customerListPdf.html.twig', [
            'cutomerDetails' => $customerDetails,
            'logo' => $Logo,
            'facebook_logo' => $facebookLogo,
            'instagram_logo' => $instagramLogo
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        return new Response(
            $pdfOutput,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="categories list.pdf"',
            ]
        );
    }


    #[Route('/categories', name: 'categories_pdf')]
    public function categoryListPdf(): Response
    {
        
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($pdfOptions);

        $categoryDetails = $this->categoriesRepository->findAll();

        // File info object for MIME type detection
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        // Process categories: decode images and determine MIME type
        $processedCategories = array_map(function ($category) use ($finfo) {
            $imageData = stream_get_contents($category->getCategoryImage());
            $mimeType = $finfo->buffer($imageData); // Detect MIME type

            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'categoryImage' => base64_encode($imageData),
                'mimeType' => $mimeType,
            ];
        }, $categoryDetails);

        // Encode logos
        $Logo = base64_encode(file_get_contents(getcwd() . '/img/Logo/Logo.png'));
        $facebookLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/FaceBook.png'));
        $instagramLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/Instagram.png'));

        // Render HTML for PDF
        $html = $this->renderView('Pdf/categoriesListPdf.html.twig', [
            'categoryDetails' => $processedCategories,
            'logo' => $Logo,
            'facebook_logo' => $facebookLogo,
            'instagram_logo' => $instagramLogo
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        return new Response(
            $pdfOutput,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="categories_list.pdf"',
            ]
        );
    }




}
