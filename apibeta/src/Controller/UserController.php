<?php

namespace App\Controller;


use App\Repository\UserRepository;
use App\Utilities\ApiFunctions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("api/list", name="list_users", methods={"POST"})
     */
    public function index(UserRepository $repository,SerializerInterface $serializer) 
    {
        $elements = $repository->findAll();
        $resultat = $serializer->serialize(
            $elements,
            'json',
            [
            ]
        );
        return new JsonResponse($resultat,200,[],true);
    }

     /**
     * @Route("api/connect", name="connect_users", methods={"POST"})
     */
    public function api_connect(EntityManagerInterface $manager, Request $request, UserRepository $repository,SerializerInterface $serializer) 
    {
        $user=$this->getUser();
        $utility = new ApiFunctions;
        
        $apiToken = $utility->genererToken($user);
        
        $user->setToken($apiToken); // je set un token au user
        
        $manager->flush($user);       // j'update le token du user

        return new JsonResponse([
            'token' => $apiToken
            
        ]);
    }
}
