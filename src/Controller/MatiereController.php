<?php

namespace App\Controller;

use App\Entity\AvisCours;
use App\Repository\AvisCoursRepository;
use App\Repository\CoursRepository;
use App\Repository\MatiereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/matiere', name: 'matiere_')]
class MatiereController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(MatiereRepository $repositoryMatiere,CoursRepository $coursRepository,AvisCoursRepository $avisCoursRepository): Response
    {
        $list = $repositoryMatiere->findAll();
        $notes = [];
        foreach($list as $matiere)
        {
            $sumNote = 0;
            $nbNote = 0;
            $cours = $coursRepository->findBy(['matiere' => $matiere]);

            foreach($cours as $currentCours)
            {
                $avis = $avisCoursRepository->findBy(['cours' => $currentCours]);

                foreach($avis as $currentAvis)
                {
                    $nbNote+=1;
                    $sumNote+=$currentAvis->getNote();
                }
            }

            if($nbNote == 0)
            {$avgNote =  "Aucune note";}
            else
            {$avgNote =  $sumNote/$nbNote;}
            

            array_push($notes,$avgNote);
        }
        return $this->render('matiere/list.html.twig', [
            'list' => $list ,
            'notes' => $notes
        ]);
    }
}
