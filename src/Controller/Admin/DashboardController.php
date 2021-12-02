<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Article;
use App\Entity\Message;
use App\Entity\Category;
use App\Controller\Admin\UserCrudController;
use App\Entity\Blacklist;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        // $routeBuilder = $this->get(AdminUrlGenerator::class);
        // return $this->redirect($routeBuilder->setController(UserCrudController::class)->generateUrl());
        return parent::index();
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DonneLaPatte Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Accueil site', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Annonces', 'fas fa-user-edit', Annonce::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-paw', Article::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Messages', 'fas fa-envelope', Message::class);
        yield MenuItem::linkToCrud('Blacklist', 'fas fa-skull-crossbones', Blacklist::class);
    }
}
