<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminRegistrationController extends AbstractController
{
    #[Route('/admin/registration', name: 'admin_registration')]
    public function index(): Response
    {
        return $this->render('admin_registration/index.html.twig');
    }
}
