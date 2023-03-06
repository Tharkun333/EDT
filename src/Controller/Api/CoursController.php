<?php

namespace App\Controller\Api;

use App\Entity\Cours;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cours', name: 'api_cour_')]

class CoursController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CoursRepository $repository): JsonResponse
    {
        $professeurs = $repository->findAll();
          return $this->json($professeurs, Response::HTTP_OK);
    }
}
