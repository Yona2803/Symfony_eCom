<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Users;
use App\Service\UsersService;
use App\Form\UsersType;

class ContactController extends AbstractController
{
    private $usersService;

    public function __construct(
        UsersService $usersService,
    ) {
        $this->usersService = $usersService;
    }


    #[Route('/Contact', name: 'ContactUs')]
    public function ContactUs(EntityManagerInterface $entityManager): Response
    {
        $User_id = $this->usersService->getIdOfAuthenticatedUser();

        if ($User_id !== null) {
            $User_Data = $entityManager->getRepository(Users::class)->find($User_id);
            return $this->render('Pages/ContactPage/ContactPage.html.twig', [
                'FirstName' => $User_Data,
                'Email' => $User_Data,
                'PhoneNumber' => $User_Data,
            ]);
        } else {
            return $this->render('Pages/ContactPage/ContactPage.html.twig', [
                'FirstName' => '',
                'Email' => '',
                'PhoneNumber' => '',
            ]);
        };
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
