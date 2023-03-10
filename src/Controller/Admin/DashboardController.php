<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Professeur;
use App\Entity\Avis;
use App\Entity\Cours;
use App\Entity\Matiere;
use App\Entity\Salle;
use App\Entity\Type;
use App\Entity\AvisCours;
use App\Entity\User;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ProfesseurCrudController::class)->generateUrl());
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);
        $userMenu->setMenuItems([]);

        return $userMenu;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Emploi du temps');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('Utilisateur'),
            MenuItem::linkToCrud('Compte', 'fas fa-chalkboard-user', User::class),
            MenuItem::linkToCrud('Professeur', 'fas fa-chalkboard-teacher', Professeur::class),

            MenuItem::section('Gestion'),
            MenuItem::linkToCrud('Matiere', 'fas fa-list-alt', Matiere::class),
            MenuItem::linkToCrud('Cours', 'fas fa-book-open', Cours::class),
            MenuItem::linkToCrud('Salle', 'fas fa-map-signs', Salle::class),
            MenuItem::linkToCrud('Type', 'fas fa-tags', Type::class),
            
            MenuItem::section('Avis'),
            MenuItem::linkToCrud('Avis des professeurs', 'fas fa-star', Avis::class),
            MenuItem::linkToCrud('Avis des cours', 'fas fa-star', AvisCours::class),

            MenuItem::section('Menu'),
            MenuItem::linkToRoute('Application', 'fa fa-chevron-left','app_login'),

        ];
    }
}
