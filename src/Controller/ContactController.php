<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Users;
use App\Service\UsersService;
use App\Form\UsersType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ContactController extends AbstractController
{
    private $usersService;
    private $mailer;

    public function __construct(
        UsersService $usersService,
        MailerInterface $mailer,
    ) {
        $this->usersService = $usersService;
        $this->mailer = $mailer;
    }


    #[Route('/Contact', name: 'ContactUs', methods: ['GET'])]
    public function ContactUs(): Response
    {
        $user = $this->getUser();
    
        // Render the template with user data or empty values
        return $this->render('Pages/ContactPage/ContactPage.html.twig', [
            'FirstName'    => $user ? $user->getFirstName() : '',
            'Email'        => $user ? $user->getEmail() : '',
            'PhoneNumber'  => $user ? $user->getPhoneNumber() : '',
        ]);
    }

    #[Route('/Contact', name: 'clientSendEmail', methods: 'POST')]
    public function clientSendEmail(
        Request $request,
        EntityManagerInterface $entityManager,
    ) {
        // get data from the form
        $firstName = $request->request->get('FirstName');
        $phoneNumber = $request->request->get('PhoneNumber');
        $email = $request->request->get('Email');
        $MsgText = $request->request->get('CommentText');

        $Data = [
            'FirstName' => $firstName,
            'PhoneNumber' => $phoneNumber,
            'Email' => $email,
            'MsgText' => $MsgText
        ];

        $this->sendEmail($Data);

        return new JsonResponse([
            'status' => 'success',
            'FirstName' => $firstName,
            'PhoneNumber' => $phoneNumber,
            'Email' => $email,
            'MsgText' => $MsgText
        ]);
    }


    private function sendEmail($data): void
    {
        $ClientSide_subject = "Exclusive WebSite: Contact Section";
        $ClientSide_Message = "New Request! \r\n \r\nName: " . $data['FirstName'] . "\r\nPhone Number: " . $data['PhoneNumber']  . "\r\nEmail: " . $data['Email'] . "\r\n \r\nMessage: \r\n- " . $data['MsgText'];


        $OurSide_subject = "Exclusive WebSite";
        $OurSide_Message = "Thank you, " . $data['FirstName'] . " for contacting us! We have received your message. Our customer service team will respond within the next 24-48 hours. \r\n" . "In the meantime, feel free to check out our Summer Sale at Product section.\r\n \r\n" . "We look forward to assisting you! \r\n" . "Exclusive.";

        // Access the sender from the parameters
        $Client_Email = $data['Email'];
        $Our_Email =  $_ENV['MAILER_SENDER'];

        // send to us email:
        $ClientSide_email = (new Email())
            ->from($Client_Email)
            ->to($Our_Email)
            ->subject($ClientSide_subject)
            ->text($ClientSide_Message, 'text/plain');

        // send to client email:
        $OurSide_email = (new Email())
            ->from($Our_Email)
            ->to($Client_Email)
            ->subject($OurSide_subject)
            ->text($OurSide_Message, 'text/plain');

        $this->mailer->send($ClientSide_email);
        $this->mailer->send($OurSide_email);
    }


    public function getUserAccount(EntityManagerInterface $entityManager): Response
    {
        $User_id = $this->usersService->getIdOfAuthenticatedUser();

        $Status = "";
        // return new Response('GET.');
        $User_Data = $entityManager->getRepository(Users::class)->find($User_id);
        $form = $this->createForm(UsersType::class, $User_Data);

        return $this->render('Pages/AccountPage/AccountPage.html.twig', [
            'User_Data' => $User_Data,
            'form' => $form->createView(),
            'Status' => $Status,
        ]);
    }
}
