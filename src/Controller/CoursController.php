<?php

namespace App\Controller;

use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cours', name: 'cours_')]
class CoursController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(CoursRepository $repositoryCours): Response
    {
        $list = $repositoryCours->findAll();

        return $this->render('cours/list.html.twig', [
            'list' => $list,
        ]);
    }

    #[Route('/edt', name: 'edt')]
    public function note(): Response
    {
        return $this->render('cours/edt.html.twig');
    }

}
