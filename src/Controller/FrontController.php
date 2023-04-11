<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/template', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front.html.twig');
    }
    

    #[Route('/home', name: 'app_front2')]
    public function indexHome(): Response
    {
        return $this->render('Home/home.html.twig');
    }

}


