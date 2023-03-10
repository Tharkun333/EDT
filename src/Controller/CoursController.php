<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cours', name: 'cours_')]
class CoursController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(CoursRepository $repositoryCours): Response
    {
        $list = $repositoryCours->findAll();

        return $this->render('cours/list.html.twig', [
            'list' => $list,
        ]);
    }

    #[Route('/edt', name: 'edt')]
    public function note(): Response
    {
        return $this->render('cours/edt.html.twig');
    }

    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request, CoursRepository $coursRepository): Response
    {
        $cours= new Cours;
        $form = $this->createForm(CoursType::class,$cours);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $cours = $form->getData();
            $coursRepository->save($cours,true);

            return $this->redirectToRoute('cours_list');
        }
        return $this->render('cours/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET','POST'])]
    public function edit(Request $request, CoursRepository $coursRepository,Cours  $cours): Response
    {
        $form = $this->createForm(CoursType::class,$cours);;
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $cours = $form->getData();
            $coursRepository->save($cours,true);

            return $this->redirectToRoute('cours_list');
        }
        return $this->render('cours/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET','POST'])]
    public function delete(Request $request, CoursRepository $coursRepository, Cours $cours): Response
    {
 
        $coursRepository->remove($cours,true);
        return $this->redirectToRoute('cours_list');
    }

}
