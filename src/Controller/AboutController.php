<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{

    #[Route('/About', name: 'AboutUs', methods: ['GET'])]
    public function ContactUs(): Response
    {
        return $this->render('Pages/AboutUsPage/AboutUsPage.html.twig');
    }
}