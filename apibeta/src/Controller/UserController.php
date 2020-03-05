<?php

namespace App\Controller;


use App\Entity\User;
use App\Utilities\ApiFunctions;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("api/list", name="list_users", methods={"GET"})
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
     * @Route("/api/connect", name="connect_users", methods={"POST","GET"})
     */
    public function api_connect(EntityManagerInterface $manager) 
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

    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function register_user(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        $data = $request->getContent();
       
        $user = $serializer->deserialize($data,User::class,'json');
        
        $haspass= $encoder->encodePassword($user,$user->getPlainPassword());
        $user->setPassword($haspass);
        $user->setPlainPassword("");
        
        //gestion des erreurs de validation
        $errors =  $validator->validate($user);
        if(count($errors)){
            $errorJson = $serializer->serialize($errors,'json');
            return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);

        }else{
            $manager->persist($user);
            $manager->flush();
            return new JsonResponse("ajouté",Response::HTTP_CREATED,[
            ],true);            
        }
    }

}
