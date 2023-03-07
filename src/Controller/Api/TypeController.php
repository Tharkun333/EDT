<?php

namespace App\Controller\Api;

use App\Entity\Type;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/type', name: 'api_type_')]

class TypeController extends AbstractController
{
    /* GET ALL */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(TypeRepository $repository): JsonResponse
    {
        $types = $repository->findAll();
        return $this->json($types,Response::HTTP_OK);
    }

    /* GET ONE */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?Type $type): JsonResponse
    {
        return !is_null($type) ? $this->json($type, Response::HTTP_OK)
        : $this->json(['message' => 'Cet type est introuvable'],Response::HTTP_NOT_FOUND);
    }

    /* POST */
    #[Route('/create/{nom}', name: 'create', methods: ['POST'])]
    public function create($nom,TypeRepository $typeRepository,ValidatorInterface $validatorInterface ) : JsonResponse
    {
        $type = new Type;

        $type->setNom($nom);

        $errors = $validatorInterface->validate($type);

        if ($errors->count() > 0) {
            $messages = [];
            foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
            }
                return $this->json($messages, Response::HTTP_BAD_REQUEST);
        }

        $typeRepository->save($type,true);

        return $this->json($type, Response::HTTP_OK);
    }

    /* DELETE */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(?Type $type,Request $request,TypeRepository $typeRepository,ValidatorInterface $validatorInterface): JsonResponse
    {
        if($type == null)
        {
            return $this->json('Aucun type n\'as était trouvé', Response::HTTP_BAD_REQUEST);
        }

        $typeRepository->remove($type,true);

        
        return $this->json('', Response::HTTP_OK);
    }
}
