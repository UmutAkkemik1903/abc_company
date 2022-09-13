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
     * @Route("/users", name="app_user")
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
    /**
     * @Route("/users/create", name="user_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
     $user = new User();
     $param = json_decode($request->getContent(),true);

     $user->setEmail($param['email']);
     $user->setPassword(bcrypt($param['password']));
     $em = $this->getDoctrine()->getManager();
     $em->persist($user);
     $em->flush();

     return $this->json('Successfully');

    }

}
