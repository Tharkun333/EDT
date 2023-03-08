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
use PhpParser\Node\Expr\Cast\Array_;
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

    #[Route('/{id}/avis/stats', name: 'get_avis_stats', methods: ['GET'])]
    public function getAvisStats(?Professeur $professeur,AvisRepository $avisRepository): JsonResponse
    {
        if($professeur == null)
        {return $this->json(['message'=>'Impossible de trouver le professeur'],Response::HTTP_BAD_REQUEST);}

        $avis = [];
        $avis = $avisRepository->findAllByProfesseur($professeur->getId());
        $max = 0; $min = 5;$sum = 0;$nbAvis=0;

        foreach($avis as $avisCourant)
        {
            $nbAvis+=1;
            $sum+=$avisCourant->getNote();
            if($max<$avisCourant->getNote()){$max = $avisCourant->getNote();}
            if($min>$avisCourant->getNote()){$min = $avisCourant->getNote();}
        }

        return $this->json([
            'avis'=>$avis,
            'max'=>$max,
            'min'=>$min,
            'nbAvis'=>$nbAvis,
            'avg'=>$sum/$nbAvis],Response::HTTP_BAD_REQUEST);
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
