<?php

namespace App\Controller\Api;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/avis', name: 'avis_')]
class AvisController extends AbstractController
{
    /* GET ALL */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Avis $avis, AvisRepository $avisRepository): JsonResponse
    {
        if(!is_null($avis)){
            $avisRepository->remove($avis, true);       
            return $this->json([], Response::HTTP_OK);
        } 
        else
        {
            return $this->json(['message' => 'Erreur'],Response::HTTP_NOT_FOUND);
        }

    }

    /* GET ONE */
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Avis $avis, AvisRepository $avisRepository, Request $request,  ValidatorInterface $validatorInterface): JsonResponse
    {
        if(!is_null($avis)){
            $data = json_decode($request->getContent(), true);
            if(is_null($data)){
                return $this->json(['message'=>'requête mal formattée'],Response::HTTP_BAD_REQUEST);
            }
            $avis->fromArray($data);

            $errors = $validatorInterface->validate($avis);

            if ($errors->count() > 0) {
                $messages = [];
                foreach ($errors as $error) {
                    $messages[$error->getPropertyPath()] = $error->getMessage();
                }
                return $this->json($messages, Response::HTTP_BAD_REQUEST);
            }
            $avisRepository->save($avis, true);
            return $this->json($avis, Response::HTTP_CREATED);
        } 
        else
        {
            return $this->json(['message' => 'Erreur'],Response::HTTP_NOT_FOUND);
        }

    }
}
