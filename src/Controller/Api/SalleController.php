<?php

namespace App\Controller\Api;

use App\Entity\Salle;
use App\Repository\SalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/salle', name: 'api_salle_')]

class SalleController extends AbstractController
{
    /* GET ALL */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(SalleRepository $repository): JsonResponse
    {
        $salles = $repository->findAll();
        return $this->json($salles,Response::HTTP_OK);
    }

    /* GET ONE */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?Salle $salle): JsonResponse
    {
        return !is_null($salle) ? $this->json($salle, Response::HTTP_OK)
        : $this->json(['message' => 'Cette salle est introuvable'],Response::HTTP_NOT_FOUND);
    }

    /* GET SPECIAL */
    #[Route('/numero/{numero}', name: 'show', methods: ['GET'])]
    public function getByNumero($numero,SalleRepository $salleRepository): JsonResponse
    {
        if($numero == null || $numero == '')
        {return $this->json(['message' => 'Numero vide ou null'],Response::HTTP_BAD_REQUEST);}

        $salle = $salleRepository->findBy(array('numero' => $numero));

        if($salle == null)
        {return $this->json(['message' => 'Salle introuvable'],Response::HTTP_NOT_FOUND);}


        return $this->json($salle,Response::HTTP_OK);
    }

    /* POST */
    #[Route('/create/{numero}', name: 'create', methods: ['POST'])]
    public function create($numero,SalleRepository $salleRepository,ValidatorInterface $validatorInterface ) : JsonResponse
    {
        $salle = new Salle;

        if($salleRepository->findBy(array('numero' => $numero)) != null)
        {return $this->json(['message' => 'Numero de salle déjà utilisé'],Response::HTTP_BAD_REQUEST);}

        $salle->setNumero($numero);

        $errors = $validatorInterface->validate($salle);

        if ($errors->count() > 0) {
            $messages = [];
            foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
            }
                return $this->json($messages, Response::HTTP_BAD_REQUEST);
        }

        $salleRepository->save($salle,true);

        return $this->json($salle, Response::HTTP_OK);
    }

    /* DELETE */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(?Salle $salle,Request $request,SalleRepository $salleRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        if($salle == null)
        {
            return $this->json('Aucune salle n\'as était trouvée', Response::HTTP_BAD_REQUEST);
        }

        $salleRepository->remove($salle,true);

        
        return $this->json('', Response::HTTP_OK);
    }
}
