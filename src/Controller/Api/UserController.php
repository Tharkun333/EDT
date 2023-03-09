<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    #[Route('/api/user', name: 'app_api_user')]
    public function getConnectedRole()
    {
        $security = new Security($this->container);
        $user = $security->getUser();
        
        return $this->json($user, Response::HTTP_OK);
    }
}
