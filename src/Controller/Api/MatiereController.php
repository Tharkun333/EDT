<?php

namespace App\Controller\Api;

use App\Entity\Matiere;
use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/matiere', name: 'api_matiere_')]
class MatiereController extends AbstractController
{
    /* GET ALL */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(MatiereRepository $repository): JsonResponse
    {
        $matieres = $repository->findAll();
        return $this->json($matieres,Response::HTTP_OK);
    }

    /* GET ONE */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?Matiere $matiere): JsonResponse
    {
        return !is_null($matiere) ? $this->json($matiere, Response::HTTP_OK)
        : $this->json(['message' => 'Cette matiere est introuvable'],Response::HTTP_NOT_FOUND);
    }

     /* POST */
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create( Request $request,MatiereRepository $matiereRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $matiere = new Matiere;

        $matiere->setTitre($data['titre']);
        $matiere->setReference($data['reference']);

        $errors = $validatorInterface->validate($matiere);

        if ($errors->count() > 0) {
            $messages = [];
            foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
            }
                return $this->json($messages, Response::HTTP_BAD_REQUEST);
        }

        $matiereRepository->save($matiere,true);

        return $this->json($matiere, Response::HTTP_OK);
    }

    /* DELETE */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(?Matiere $matiere,Request $request,MatiereRepository $matiereRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        if($matiere == null)
        {
            return $this->json('Aucune matiere n\'as était trouvée', Response::HTTP_BAD_REQUEST);
        }

        $matiereRepository->remove($matiere,true);

        
        return $this->json('', Response::HTTP_OK);
    }

}
