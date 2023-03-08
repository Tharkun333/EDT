<?php

namespace App\Controller\Api;

use App\Entity\AvisCours;
use App\Repository\AvisCoursRepository;
use App\Repository\CoursRepository;
use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/avisCours', name: 'api_avisCours_')]
class AvisCoursController extends AbstractController
{
    /* GET ALL */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(AvisCoursRepository $repository): JsonResponse
    {
        $avisCours = $repository->findAll();
          return $this->json($avisCours, Response::HTTP_OK);
    }

    /* GET ONE */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?AvisCours $avisCours): JsonResponse
    {
        return !is_null($avisCours) ? $this->json($avisCours, Response::HTTP_OK)
        : $this->json(['message' => 'Cet avis sur un cours est introuvable'],Response::HTTP_NOT_FOUND);
    }

    /* POST */
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create( Request $request,AvisCoursRepository $avisCoursRepository,CoursRepository $coursRepository, ValidatorInterface $validatorInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $avisCours = new AvisCours;

        $cours = $coursRepository->find($data['cours_id']);
        if($cours == null)
        {return $this->json(['message' => 'Cours introuvable'], Response::HTTP_BAD_REQUEST);} 
        $avisCours->setCours($cours);
        
        $avisCours->setCommentaire($data['commentaire']);
        $avisCours->setEmailEtudiant($data['emailEtudiant']);
        $avisCours->setNote($data['note']);

        $errors = $validatorInterface->validate($avisCours);

        if ($errors->count() > 0) {
            $messages = [];
            foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
            }
                return $this->json($messages, Response::HTTP_BAD_REQUEST);
        }

        $avisCoursRepository->save($avisCours,true);

        return $this->json($avisCours, Response::HTTP_OK);
    }

    /* DELETE */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(?AvisCours $avisCours,Request $request,AvisCoursRepository $avisCoursRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        if($avisCours == null)
        {
            return $this->json('Aucun avis de cours n\'as était trouvé', Response::HTTP_BAD_REQUEST);
        }

        $avisCoursRepository->remove($avisCours,true);

        
        return $this->json('', Response::HTTP_OK);
    }
}
