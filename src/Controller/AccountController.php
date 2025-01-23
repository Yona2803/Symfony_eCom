<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Users;
use App\Form\UsersType;



class AccountController extends AbstractController
{
    // GET Route to display the form
    #[Route('/MyAccount/{User_id}', name: 'MyAccountPage', methods: ['GET'])]
    public function getUserAccount(int $User_id, EntityManagerInterface $entityManager): Response
    {
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
    #[Route('/MyAccount/{User_id}/update', name: 'UpdateAccountPage', methods: ['POST'])]
    public function postUserAccount(
        int $User_id,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
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

    #[Route('/HashPasword/{Password}', name: 'test_hash')]
    public function testHash(string $Password, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Create a dummy user that implements PasswordAuthenticatedUserInterface
        $user = new Users(); // Replace with your user entity
        // Hash the password '123'
        $hashed = $passwordHasher->hashPassword($user, $Password);
        // Return the hashed password in the response
        return new Response("Hashed Password: " . $hashed);
    }
}
