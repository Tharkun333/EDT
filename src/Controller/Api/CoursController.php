<?php

namespace App\Controller\Api;

use App\Entity\Cours;
use App\Repository\AvisCoursRepository;
use App\Repository\CoursRepository;
use App\Repository\MatiereRepository;
use App\Repository\ProfesseurRepository;
use App\Repository\SalleRepository;
use App\Repository\TypeRepository;
use DateTimeZone;
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
    /* GET ALL */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CoursRepository $repository): JsonResponse
    {
        $professeurs = $repository->findAll();
          return $this->json($professeurs, Response::HTTP_OK);
    }

    /* GET ONE */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?Cours $cours): JsonResponse
    {
        return !is_null($cours) ? $this->json($cours, Response::HTTP_OK)
        : $this->json(['message' => 'Ce cours est introuvable'],Response::HTTP_NOT_FOUND);
    }

    /* GET SPECIAL */
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

    #[Route('/{id}/avis/stats', name: 'get_avis_stats', methods: ['GET'])]
    public function getAvisStats(?Cours $cours,AvisCoursRepository $avisCoursRepository): JsonResponse
    {
        if($cours == null)
        {return $this->json(['message'=>'Impossible de trouver le cours'],Response::HTTP_BAD_REQUEST);}

        $avis = [];
        $avis = $avisCoursRepository->findAllByCours($cours->getId());
        $max = 0; $min = 5;$sum = 0;$nbAvis=0;

        foreach($avis as $avisCourant)
        {
            $nbAvis+=1;
            $sum+=$avisCourant->getNote();
            if($max<$avisCourant->getNote()){$max = $avisCourant->getNote();}
            if($min>$avisCourant->getNote()){$min = $avisCourant->getNote();}
        }

        return $this->json([
            'max'=>$max,
            'min'=>$min,
            'nbAvis'=>$nbAvis,
            'avg'=>$sum/$nbAvis],Response::HTTP_BAD_REQUEST);
    }

    /* POST */
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create( Request $request,MatiereRepository $matiereRepository,CoursRepository $coursRepository,ProfesseurRepository $professeurRepository,SalleRepository $salleRepository,TypeRepository $typeRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $cours = new Cours;

        $cours->setMatiere($matiereRepository->find($data['id_matiere']));
        $cours->setSalle($salleRepository->find($data['id_salle']));
        $cours->setProfesseur($professeurRepository->find($data['id_professeur']));
        $cours->setType($typeRepository->find($data['id_type']));

        $dateDeb = new \DateTimeImmutable($data['dateHeureDebut']);
        $dateFin = new \DateTimeImmutable($data['dateHeureFin']);

        $cours->setDateHeureDebut($dateDeb);
        $cours->setDateHeureFin($dateFin);

        $errors = $validatorInterface->validate($cours);

        if ($errors->count() > 0) {
            $messages = [];
            foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
            }
                return $this->json($messages, Response::HTTP_BAD_REQUEST);
        }

        $coursRepository->save($cours,true);

        return $this->json($cours, Response::HTTP_OK);
    }

    /* DELETE */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(?Cours $cours,Request $request,CoursRepository $coursRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        if($cours == null)
        {
            return $this->json('Aucun cours n\'as ??tait trouv??', Response::HTTP_BAD_REQUEST);
        }

        $coursRepository->remove($cours,true);

        
        return $this->json('', Response::HTTP_OK);
    }
    

    #[Route('/getBySalleAndDate', name: 'getBySalleAndDate', methods: ['POST'])]
    public function getByDateAndSalle(Request $request,CoursRepository $coursRepository,SalleRepository $salleRepository, ValidatorInterface $validatorInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dateDeb = \DateTime::createFromFormat('Y-m-d H:i:s', $data['dateDeb'],new DateTimeZone('UTC'));
        $dateFin = \DateTime::createFromFormat('Y-m-d H:i:s', $data['dateFin'],new DateTimeZone('UTC'));
        $coursAtThisDateInThisSalle = $coursRepository->getByDateAndSalle($dateDeb,$salleRepository->find($data['salle']));


        foreach($coursAtThisDateInThisSalle as $cours)
        {
            if(($dateDeb > $cours->getDateHeureDebut() && $dateDeb < $cours->getDateHeureFin()) || ($dateDeb < $cours->getDateHeureDebut() && $dateFin > $cours->getDateHeureDebut()))
            {return $this->json(['response' => false],Response::HTTP_OK);}
        }
        return $this->json(['response' => true],Response::HTTP_OK);
    }
    
}