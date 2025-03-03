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

        $cutomerDetails = $this->usersRepository->findAll();

        $Logo = base64_encode(file_get_contents(getcwd() . '/img/Logo/Logo.png'));
        $facebookLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/FaceBook.png'));
        $instagramLogo = base64_encode(file_get_contents(getcwd() . '/img/icon/Instagram.png'));

        $html = $this->renderView('Pdf/customerListPdf.html.twig', [
            'cutomerDetails' => $cutomerDetails,
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
                'Content-Disposition' => 'inline; filename="customers list.pdf"',
            ]
        );
    }


}
