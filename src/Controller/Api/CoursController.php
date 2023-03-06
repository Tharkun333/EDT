<?php

namespace App\Controller\Api;

use App\Entity\Cours;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/cours', name: 'api_cour_')]

class CoursController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CoursRepository $repository): JsonResponse
    {
        $professeurs = $repository->findAll();
          return $this->json($professeurs, Response::HTTP_OK);
    }

    #[Route('/create', name: 'create', methods: ['PUT'])]
    public function create( Request $request,CoursRepository $coursRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        //TODO
        // $cours = (new Cours)
        // ->from($data);

        // $errors = $validatorInterface->validate($cours);

        // if ($errors->count() > 0) {
        //     $messages = [];
        //     foreach ($errors as $error) {
        //         $messages[$error->getPropertyPath()] = $error->getMessage();
        //     }
        //     return $this->json($messages, Response::HTTP_BAD_REQUEST);
        // }
        // $avisRepository->save($avis, true);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/getByDate', name: 'getByDate', methods: ['POST'])]
    public function getByDate( Request $request,CoursRepository $coursRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $date = \DateTime::createFromFormat('Y-m-d', $data['date']);
        if (!$date) {
            return $this->json("La valeur '$date' n'est pas une date valide (format attendu : YYYY-MM-DD).", Response::HTTP_BAD_REQUEST);
        }
        $cours = $coursRepository->getByDate($date);

        return $this->json($cours, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?Cours $cours): JsonResponse
    {
        return !is_null($cours) ? $this->json($cours, Response::HTTP_OK)
        : $this->json(['message' => 'Ce cours est introuvable'],Response::HTTP_NOT_FOUND);
    }
}
