<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class SecurityController extends AbstractController
{
    public const SCOPES = [
        'google' => ['email', 'profile'],
    ];

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // Get login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // Route for the OAuth login (e.g., redirecting to Google)
    #[Route("/login", name: 'auth_oauth_login', methods: ['GET'])]
    public function loginOAuth(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render("items/test.html.twig");
    }

    // Route to start the OAuth connection (redirecting to Google)
    #[Route("/oauth/connect/{service}", name: 'auth_oauth_connect', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if (!in_array($service, array_keys(self::SCOPES), true)) {
            throw $this->createNotFoundException();
        }

        // Choise the google account you want to login with.
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], [
                'prompt' => 'select_account',
            ]);

        // This login directly with the last login google account.
        // return $clientRegistry
        //     ->getClient($service)
        //     ->redirect(self::SCOPES[$service], []);
    }



    // OAuth callback route (handling authentication after OAuth)
    #[Route('/oauth/check/{service}', name: 'auth_oauth_check', methods: ['GET', 'POST'])]
    public function check(string $service, ClientRegistry $clientRegistry): Response
    {
        // try {
        //     // Fetch the user from the OAuth provider (Google)
        //     $client = $clientRegistry->getClient($service);
        //     $user = $client->fetchUser();

        //     // Do something with the user, such as logging them in
        //     // You could save them to the database or simply authenticate them
        //     $this->get('security.token_storage')->setToken($user);

        //     // Redirect to the home page or wherever you want after a successful login
        //     return $this->redirectToRoute('home');
        // } catch (AuthenticationException $e) {
        //     // Handle authentication errors (e.g., invalid token, error in fetching user)
        //     return $this->redirectToRoute('app_login');
        // }
        return new Response(status: 200);
    }
}
