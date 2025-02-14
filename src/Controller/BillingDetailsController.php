<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Items;
use App\Service\ItemsService;
use App\Entity\Carts;
use App\Entity\CartItems;
use App\Entity\Orders;
use App\Entity\OrderDetails;
use App\Entity\Users;
use App\Service\UsersService;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;
use Dompdf\Options;
use SebastianBergmann\Environment\Console;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BillingDetailsController extends AbstractController
{
    private ItemsService $itemsService;
    private $usersService;
    private $mailer;
    private $params;

    public function __construct(
        ItemsService $itemsService,
        UsersService $usersService,
        MailerInterface $mailer,
        ParameterBagInterface $params
    ) {
        $this->itemsService = $itemsService;
        $this->usersService = $usersService;
        $this->mailer = $mailer;
        $this->params = $params;
    }

    #[Route('/CheckOut', name: 'CheckOut', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Get User Data
        $User_id = $this->usersService->getIdOfAuthenticatedUser();
        $User_Data = $entityManager->getRepository(Users::class)->find($User_id);

        return $this->render('Pages/CartPage/BillingDetails/BillingDetails.html.twig', [
            'User_Data' => $User_Data,
        ]);
    }

    #[Route('/CheckOut/PlaceOrder', name: 'PlaceOrder', methods: ['POST'])]
    public function placeOrder(
        Request $request,
        EntityManagerInterface $entityManager,
        OrderDetailsRepository $orderDetailsRepository,
    ) {
        $User_id = $this->usersService->getIdOfAuthenticatedUser();
        $user = $entityManager->getRepository(Users::class)->find($User_id);
        $errors = [];
        $OrderDetails = [];
        $Ht = 0;

        if (!$User_id) {
            $errors[] = 'User_id is not authenticated';
        } else {
            // Get cart items from the request
            $cartItems = $request->request->all('cartItems');

            // Get form data
            $formData = [
                'firstName' => $request->request->get('users_firstName'),
                'lastName' => $request->request->get('users_lastName'),
                'address' => $request->request->get('users_address'),
                'phoneNumber' => $request->request->get('users_phoneNumber'),
                'email' => $request->request->get('users_email'),
                'ClientId' => $User_id,
                'TTCValue' => $request->request->get('TTCValue'),
            ];

            // validation
            $errors = $this->validateFormData($formData, $cartItems);

            // If no errors, process the form
            if (empty($errors)) {
                foreach ($cartItems as $item) {
                    // Prepare Necessary Data
                    if (empty($item['id']) || empty($item['price'])) {
                        $errors[] = 'MissingItem';
                    }

                    $product = $entityManager->getRepository(Items::class)->find($item['id']);
                    if (!$product) {
                        $errors[] = 'Product' . $item['id'];
                    } else {
                        $OrderDetails[] = [
                            'id' => $item['id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ];
                        $Ht +=  $item['price'];
                    }
                }

                // Code Logic
                if (empty($errors)) {
                    $orderData = $this->syncOrders($user, $OrderDetails, $formData['TTCValue'], $Ht, $orderDetailsRepository, $entityManager);

                    $data = [
                        'status' => 'success',
                        'Client' =>  $formData,
                        'data' => $orderData,
                    ];
                    $filePath = $this->generatePdf($data);


                    $this->deleteUserCart($user, $entityManager);

                    return new JsonResponse([
                        'status' => 'success',
                        'filePath' => $filePath
                    ]);
                }
            }
        }

        return new JsonResponse([
            'status' => empty($errors) ? 'success' : 'error',
            'errors' => $errors,
        ]);
    }
    private function validateFormData(array $formData, array $cartItems): array
    {
        $errors = [];

        if (empty($formData['firstName'])) {
            $errors[] = 'FirstName';
        }

        $formData['lastName'] = $formData['lastName'] ?? '';

        if (empty($formData['address'])) {
            $errors[] = 'Address';
        }
        if (empty($formData['phoneNumber'])) {
            $errors[] = 'PhoneNumber';
        }
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email';
        }
        if (empty($cartItems)) {
            $errors[] = 'Cart';
        }

        return $errors;
    }
    private function syncOrders(Users $user, array $OrderDetails, float $TTCValue, $Ht, OrderDetailsRepository $orderDetailsRepository, EntityManagerInterface $entityManager): array
    {
        $Order = $this->createNewOrder($user, $TTCValue, $entityManager);

        foreach ($OrderDetails as $itemData) {
            $this->addOrderDetails($Order, $itemData, $entityManager);
        }

        $orderData = $this->prepareResponse($Order, $OrderDetails, $Ht, $entityManager);

        $entityManager->flush();

        return $orderData;
    }
    private function createNewOrder(Users $user, $TTCValue, EntityManagerInterface $entityManager): Orders
    {
        $Order = new Orders();
        $Order->setUser($user);
        $Order->setTotalAmount($TTCValue);
        $Order->setOrderDate(new \DateTime());

        $entityManager->persist($Order);
        $entityManager->flush();

        return $Order;
    }
    private function addOrderDetails(Orders $Order, array $itemData, EntityManagerInterface $entityManager): void
    {
        $item = $entityManager->getRepository(Items::class)->find($itemData['id']);

        if (!$item) {
            throw new \Exception("Item not found: " . $itemData['id']);
        }

        $OrderDetail = new OrderDetails();
        $OrderDetail->setOrderFk($Order);
        $OrderDetail->setItem($item);

        $UpdateItemQte = $this->UpdateItemQte($item, $itemData['quantity'], $entityManager);

        $OrderDetail->setQuantity($UpdateItemQte['Qte']);
        $OrderDetail->setTotalPrice($UpdateItemQte['TotalPrice']);

        $entityManager->persist($OrderDetail);
    }
    private function UpdateItemQte($item, $Qte, EntityManagerInterface $entityManager): array
    {
        $FinalQte = 0;
        $itemStock = $item->getStock();

        if ($itemStock >= $Qte) {
            $item->setStock($itemStock - $Qte);
            $FinalQte = $Qte;
        } else {
            $item->setStock(0);
            $FinalQte = $itemStock;
        }

        $entityManager->persist($item);
        $entityManager->flush();

        $ReturnedValue = [
            'Qte' => $FinalQte,
            'TotalPrice' => $FinalQte * $item->getPrice(),
        ];

        return $ReturnedValue;
    }
    private function prepareResponse(Orders $Order, array $OrderDetails, $Ht, EntityManagerInterface $entityManager): array
    {
        $orderData = [
            'order_id' => $Order->getId(),
            'order_date' => $Order->getOrderDate()->format('d/m/Y'),
            'TTC' => $Order->getTotalAmount(),
            'HT' => $Ht,
            'items' => [],
        ];

        foreach ($OrderDetails as $itemData) {
            $product = $entityManager->getRepository(Items::class)->find($itemData['id']);

            if (!$product) {
                throw new \Exception("Product not found: " . $itemData['id']);
            }

            $itemImage = $product->getItemImage();
            $base64Image = '';

            // Check if the image exists
            if ($itemImage) {
                if (is_resource($itemImage)) {
                    $itemImage = stream_get_contents($itemImage);
                }

                // Detect the MIME type using finfo_buffer
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $itemImage);
                finfo_close($finfo);

                // Assign the correct MIME type for base64 encoding
                if ($mimeType === 'image/png') {
                    $imageMimeType = 'image/png';
                } elseif (in_array($mimeType, ['image/jpeg', 'image/jpg'])) {
                    $imageMimeType = 'image/jpeg';
                }

                // Base64 encode the image data
                if (!empty($imageMimeType)) {
                    $base64Image = base64_encode($itemImage);
                }
            }

            $orderData['items'][] = [
                'item_id' => $product->getId(),
                'item_name' => $product->getName(),
                'item_img' => $base64Image ? 'data:' . $imageMimeType . ';base64,' . $base64Image : '',
                'quantity' => $itemData['quantity'],
                'price' =>   $itemData['price'] / $itemData['quantity'],
                'TotalePrice' => $itemData['price'],
            ];
        }
        return $orderData;
    }
    private function generatePdf($data)
    {
        $publicDir = $this->getParameter('kernel.project_dir') . '/public/Pdf/Orders/';
        // Add logging to debug
        error_log("Attempting to delete files from directory: " . $publicDir);

        if (is_dir($publicDir)) {
            $files = glob($publicDir . '*');
            error_log("Found " . count($files) . " files");

            foreach ($files as $file) {
                if (is_file($file)) {
                    try {
                        if (unlink($file)) {
                            error_log("Successfully deleted: " . $file);
                        } else {
                            error_log("Failed to delete: " . $file);
                        }
                    } catch (\Exception $e) {
                        error_log("Error deleting file " . $file . ": " . $e->getMessage());
                    }
                }
            }
        } else {
            error_log("Directory does not exist: " . $publicDir);
        }


        $pdfFileName = 'ORDER ' . $data['data']['order_id'] . '.pdf';
        $pdfFilePath = $publicDir . $pdfFileName;

        // Create the directory if it doesn't exist
        $filesystem = new Filesystem();
        if (!$filesystem->exists($publicDir)) {
            $filesystem->mkdir($publicDir);
        }

        // Read image files and encode them to base64
        $logoPath = $this->getParameter('kernel.project_dir') . '/public/img/Logo/Logo.png';
        $noImgPath = $this->getParameter('kernel.project_dir') . '/public/img/No_Img.png';
        $fbPath = $this->getParameter('kernel.project_dir') . '/public/img/icon/FaceBook.png';
        $instaPath = $this->getParameter('kernel.project_dir') . '/public/img/icon/Instagram.png';

        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        } else {
            $logoBase64 = '';
        }

        if (file_exists($noImgPath)) {
            $noImgBase64 = base64_encode(file_get_contents($noImgPath));
        } else {
            $noImgBase64 = '';
        }

        if (file_exists($fbPath)) {
            $fbBase64 = base64_encode(file_get_contents($fbPath));
        } else {
            $fbBase64 = '';
        }
        if (file_exists($instaPath)) {
            $instaBase64 = base64_encode(file_get_contents($instaPath));
        } else {
            $instaBase64 = '';
        }

        // Render the HTML template and pass the data array along with the Base64 images
        $html = $this->renderView('Pdf/OrderSummary.html.twig', [
            'data' => $data,
            'logo' => $logoBase64,
            'noImg' => $noImgBase64,
            'faceBook' => $fbBase64,
            'instagram' => $instaBase64,
        ]);;

        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream the PDF as a downloadable file
        $output = $dompdf->output();
        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');

        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="ORDER #' . $data['data']['order_id'] . '.pdf"'
        );

        file_put_contents($pdfFilePath, $dompdf->output());

        $this->sendEmail($pdfFilePath, $data);

        return '/Pdf/Orders/' . $pdfFileName;
    }
    private function sendEmail($pdfFilePath, $data)
    {
        $subject = "OrderSummary";
        // Access the sender from the parameters
        $Email_From = $_ENV['MAILER_SENDER'];
        $Email_to = $data['Client']['email'];

        $renderedHtml = $this->renderView('Email/Order.html.twig', [
            'data' => $data,
        ]);

        // Then send the email:
        $email = (new Email())
            ->from($Email_From)
            ->to($Email_to)
            ->subject($subject)
            ->html($renderedHtml)
            ->attachFromPath($pdfFilePath, 'application/pdf');

        $this->mailer->send($email);
    }
    private function deleteUserCart(Users $user, EntityManagerInterface $entityManager): void
    {
        $cart = $entityManager->getRepository(Carts::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            return;
        }

        $cartItems = $entityManager->getRepository(CartItems::class)->findBy(['cart' => $cart]);

        foreach ($cartItems as $cartItem) {
            $entityManager->remove($cartItem);
        }

        $entityManager->remove($cart);
        $entityManager->flush();
    }

    #[Route('/CheckOut/CheckOutItems', name: 'CheckOutItems', methods: ['GET'])]
    public function CheckOutItems(Request $request, EntityManagerInterface $entityManager)
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
                ]
            ];
        }

        if (!$productDetails) {
            return new Response('Products : no data');
        }

        return new JsonResponse($productDetails);
    }
}
