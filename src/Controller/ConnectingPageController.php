<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Users;
use App\Form\RegistrationFormType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\UsersService;
use App\Repository\UsersRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConnectingPageController extends AbstractController
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    // **** ConnectingPage ****
    #[Route('/Connecting', name: 'ConnectingPage', methods: ['GET'])]
    public function connecting(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $user = new Users();
        $signInForm = $this->createForm(RegistrationFormType::class, $user);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Pages/ConnectingPage/ConnectingPage.html.twig', [
            'signInForm' => $signInForm->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
            'sign_in_successful' => false,
        ]);
    }

    // ** SignInPage **
    #[Route('/Connecting/SignIn', name: 'SignInPage', methods: ['POST'])]
    public function signIn(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $signInForm = $this->createForm(RegistrationFormType::class, $user);
        $signInForm->handleRequest($request);

        if ($signInForm->isSubmitted() && $signInForm->isValid()) {
            $firstName = $signInForm->get('firstName')->getData();
            $plainPassword = $signInForm->get('plainPassword')->getData();

            $user->setUsername($firstName . random_int(0, 100));  // Ensure unique username if needed
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_CUSTOMER']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('Pages/ConnectingPage/ConnectingPage.html.twig', [
                'signInForm' => $signInForm->createView(),
                'last_username' => null,
                'error' => null,
                'sign_in_successful' => true,
            ]);
        }

        // Handle failure
        return $this->render('Pages/ConnectingPage/ConnectingPage.html.twig', [
            'signInForm' => $signInForm->createView(),
            'last_username' => null,
            'error' => null,
            'sign_in_successful' => false,
        ]);
    }

    // ** LogInPage **
    #[Route(path: '/Connecting/LogIn', name: 'LogInPage')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new Users();
        $signInForm = $this->createForm(RegistrationFormType::class, $user);
        $signInForm->handleRequest($request);

        return $this->render('Pages/ConnectingPage/ConnectingPage.html.twig', [
            'signInForm' => $signInForm->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
            'sign_in_successful' => true,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
