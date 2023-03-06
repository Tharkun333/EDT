<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ProfesseurRepository;

use App\Entity\Professeur;

use App\Form\ProfesseurType;

use Symfony\Component\HttpFoundation\Request;


#[Route('/professeur', name: 'professeur_')]
class ProfesseurController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ProfesseurRepository $repositoryProfesseur): Response
    {

        $list = $repositoryProfesseur->findAll();

        return $this->render('professeur/list.html.twig', [
            'list' => $list,
        ]);
    }

    #[Route('/note', name: 'note', methods: ['GET'])]
    public function note(): Response
    {
        return $this->render('professeur/note.html.twig');
    }

    #[Route('/create', name: 'create', methods: ['GET','POST'])]
    public function create(Request $request, ProfesseurRepository $repositoryProfesseur): Response
    {
        $professeur= new Professeur;
        $form = $this->createForm(ProfesseurType::class,$professeur);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $professeur = $form->getData();
            $repositoryProfesseur->save($professeur,true);

            return $this->redirectToRoute('professeur_list');
        }
        return $this->render('professeur/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET','POST'])]
    public function edit(Request $request, ProfesseurRepository $repositoryProfesseur,int $id): Response
    {
        $professeur= $repositoryProfesseur->find($id);
        $form = $this->createForm(ProfesseurType::class,$professeur);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $professeur = $form->getData();
            $repositoryProfesseur->save($professeur,true);

            return $this->redirectToRoute('professeur_list');
        }
        return $this->render('professeur/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET','POST'])]
    public function delete(Request $request, ProfesseurRepository $repositoryProfesseur, Professeur $professeur): Response
    {
 
        $repositoryProfesseur->remove($professeur,true);
        return $this->redirectToRoute('professeur_list');
    }
}
