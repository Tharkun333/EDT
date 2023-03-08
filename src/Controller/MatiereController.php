<?php

namespace App\Controller;

use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/matiere', name: 'matiere_')]
class MatiereController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(MatiereRepository $repositoryMatiere): Response
    {
        $list = $repositoryMatiere->findAll();

        return $this->render('matiere/list.html.twig', [
            'list' => $list,
        ]);
    }
}
