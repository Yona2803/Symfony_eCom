<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Users;
use App\Form\UsersType;
use App\Service\UsersService;


class AccountController extends AbstractController
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    // GET Route to display the form
    #[Route('/MyAccount', name: 'MyAccountPage', methods: ['GET'])]

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

    // POST Route to handle form submission
    #[Route('/MyAccount', name: 'UpdateAccountPage', methods: ['POST'])]
    public function postUserAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $User_id = $this->usersService->getIdOfAuthenticatedUser();

        $Status = "";

        if (!$User_id) {
            $Status = "User_id"; // User Id not found
        } else {
            $User_Data = $entityManager->getRepository(Users::class)->find($User_id);
            if (!$User_Data) {
                $Status = "User_Data"; // User Data not found
            } else {
                $form = $this->createForm(UsersType::class, $User_Data);
                $form->handleRequest($request);

                if ($form->isSubmitted()) {
                    $currentPassword = $form->get('currentPassword')->getData();
                    if ($passwordHasher->isPasswordValid($User_Data, $currentPassword)) {
                        $newPassword = $form->get('newPassword')->getData();
                        if (!empty($newPassword)) {
                            $hashedPassword = $passwordHasher->hashPassword($User_Data, $newPassword);
                            $User_Data->setPassword($hashedPassword);
                            $Status = "hashedPassword"; // New password was updated.
                        }
                        $entityManager->persist($User_Data);
                        $entityManager->flush();
                        $Status = "ok"; // User data updated successfully.
                    } else {
                        $Status = "CurrentPassWord"; // Current password is incorrect.
                    }
                }
            }
        }

        return $this->render('Pages/AccountPage/AccountPage.html.twig', [
            'form' => $form->createView(),
            'User_Data' => $User_Data,
            'Status' => $Status,
        ]);
    }

    // **** Get/Set Btweeen local storage and DB ****
    #[Route(path: '/SyncLocalStorage', name: 'SyncLocalStorage', methods: ['POST'])]
    public function SyncLocalStorage(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        
        return new JsonResponse(
            [
                'status' => 'success',
                'data' => $data
            ]
        );
    }


    // **** hash passwords ****
    #[Route('/Hash/{Password}', name: 'test_hash')]
    public function testHash(string $Password, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Create a dummy user
        $user = new Users(); // Replace with your user entity
        // Hash the $Password
        $hashed = $passwordHasher->hashPassword($user, $Password);

        return new Response("Hashed Password: " . $hashed);
    }
}
