<?php

namespace App\Controller\Admin;

use App\Entity\Professeur;
use App\Form\Professeur1Type;
use App\Repository\ProfesseurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/prefesseur')]
class PrefesseurController extends AbstractController
{
    #[Route('/', name: 'app_admin_prefesseur_index', methods: ['GET'])]
    public function index(ProfesseurRepository $professeurRepository): Response
    {
        return $this->render('admin/prefesseur/index.html.twig', [
            'professeurs' => $professeurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_prefesseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProfesseurRepository $professeurRepository): Response
    {
        $professeur = new Professeur();
        $form = $this->createForm(Professeur1Type::class, $professeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $professeurRepository->save($professeur, true);

            return $this->redirectToRoute('app_admin_prefesseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/prefesseur/new.html.twig', [
            'professeur' => $professeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_prefesseur_show', methods: ['GET'])]
    public function show(Professeur $professeur): Response
    {
        return $this->render('admin/prefesseur/show.html.twig', [
            'professeur' => $professeur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_prefesseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Professeur $professeur, ProfesseurRepository $professeurRepository): Response
    {
        $form = $this->createForm(Professeur1Type::class, $professeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $professeurRepository->save($professeur, true);

            return $this->redirectToRoute('app_admin_prefesseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/prefesseur/edit.html.twig', [
            'professeur' => $professeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_prefesseur_delete', methods: ['POST'])]
    public function delete(Request $request, Professeur $professeur, ProfesseurRepository $professeurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$professeur->getId(), $request->request->get('_token'))) {
            $professeurRepository->remove($professeur, true);
        }

        return $this->redirectToRoute('app_admin_prefesseur_index', [], Response::HTTP_SEE_OTHER);
    }
}
