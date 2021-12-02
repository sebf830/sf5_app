<?php

namespace App\Controller\Writer;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardWriterController extends AbstractDashboardController
{
    /**
     * @Route("/writer", name="app_writer")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Articles de DonneLaPatte');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('<a class="text-success text-center" href="/home">Accueil site</a>');
        yield MenuItem::linkToCrud('Articles', 'fas fa-paw', Article::class);
    }
}
