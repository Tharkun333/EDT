<?php

namespace App\Controller;

use App\Entity\AvisCours;
use App\Entity\Cours;
use App\Entity\Professeur;
use App\Form\AvisCoursType;
use App\Repository\AvisCoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avisCours', name: 'avisCours_')]
class AvisCoursController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(AvisCoursRepository $avisCoursRepository): Response
    {
        $list = $avisCoursRepository->findAll();

        return $this->render('avisCours/list.html.twig', [
            'list' => $list,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request, AvisCoursRepository $avisCoursRepository): Response
    {
        $avisCours= new AvisCours;
        $form = $this->createForm(AvisCoursType::class,$avisCours);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $avisCours = $form->getData();
            $avisCoursRepository->save($avisCours,true);

            return $this->redirectToRoute('avisCours_list');
        }
        return $this->render('avisCours/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create/{id}', name: 'createWithProf', methods: ['GET','POST'])]
    public function createWithProf(Cours $cours,Request $request, AvisCoursRepository $avisCoursRepository): Response
    {
        $avisCours= new AvisCours;
        $avisCours->setCours($cours);
        $form = $this->createForm(AvisCoursType::class,$avisCours);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            var_dump('ughvjbk,');
            $avisCours = $form->getData();
            $avisCoursRepository->save($avisCours,true);

            return $this->redirectToRoute('avisCours_list');
        }
        return $this->render('avisCours/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET','POST'])]
    public function edit(Request $request,AvisCoursRepository $avisCoursRepository, AvisCours $avisCours): Response
    { 
        $form = $this->createForm(AvisCoursType::class,$avisCours);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $avisCours = $form->getData();
            $avisCoursRepository->save($avisCours,true);

            return $this->redirectToRoute('avisCours_list');
        }
        return $this->render('avisCours/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET','POST'])]
    public function delete(Request $request, AvisCoursRepository $avisCoursRepository, AvisCours $avisCours): Response
    {
        $avisCoursRepository->remove($avisCours,true);
        return $this->redirectToRoute('avisCours_list');
    }
}
