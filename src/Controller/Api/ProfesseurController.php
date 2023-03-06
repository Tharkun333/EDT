<?php

namespace App\Controller\Api;

use App\Entity\Avis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\ProfesseurRepository;

use App\Entity\Professeur;

use App\Form\ProfesseurType;
use App\Repository\AvisRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/professeurs', name: 'api_professeur_')]
class ProfesseurController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ProfesseurRepository $repository): JsonResponse
    {

        $professeurs = $repository->findAll();
        // $json = json_encode(array_map(fn ($professeurs)=> $professeurs->toArray(),$professeurs));
        // $response = new Response();
        // $response->setContent($json);
        // $response->setStatusCode(Response::HTTP_OK); 
        // $response->headers->set('Content-Type','application/json');
        // return $response;

        // return $this->json(array_map(fn ($professeurs)=> $professeurs->toArray(),$professeurs));
        return $this->json($professeurs, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?Professeur $professeur): JsonResponse
    {
        return !is_null($professeur) ? $this->json($professeur, Response::HTTP_OK)
        : $this->json(['message' => 'Ce professeur est introuvable'],Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}/avis', name: 'get_avis', methods: ['GET'])]
    public function getAvis(?Professeur $professeur): JsonResponse
    {
        return !is_null($professeur) ? $this->json($professeur->getAvis()->toArray(), Response::HTTP_OK)
        : $this->json(['message' => 'Ce professeur est introuvable'],Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}/avis', name: 'create_avis', methods: ['POST'])]
    public function createAvis(?Professeur $professeur, Request $request, ProfesseurRepository $professeurRepository, AvisRepository $avisRepository, ValidatorInterface $validatorInterface): Response
    {

        if(!is_null($professeur)){
            $data = json_decode($request->getContent(), true);
            if(is_null($data)){
                return $this->json(['message'=>'requête mal formattée'],Response::HTTP_BAD_REQUEST);
            }

            $avis = (new Avis)
            ->fromArray($data)
            ->setProfesseur($professeur);

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
        else return $this->json(['message' => 'Ce professeur est introuvable'],Response::HTTP_NOT_FOUND);
    }
}
