<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="app_user")
     */
    public function index(): JsonResponse
    {
        $usersRespository = $this->getDoctrine()->getRepository(User::class)->findAll();
        foreach ($usersRespository as $data){
            $res[] = [
                'id' => $data->getId(),
                'email' => $data->getEmail(),
                'roles' => $data->getRoles(),
                'password' => $data->getpassword(),
            ];
        }
        return $this->json($res);
    }

}
