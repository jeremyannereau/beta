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
use Symfony\Component\Validator\Constraints\Json;

class UserController extends AbstractController
{
   
     /**
     * @Route("api/connect", name="connect_user", methods={"POST","GET"})
     */
    public function api_connect(EntityManagerInterface $manager, Request $request) 
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
     * @Route("/api/register", name="register_user", methods={"POST"})
     */
    public function register_user(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        $data = $request->getContent();
       
        $user = $serializer->deserialize($data,User::class,'json');
        
        $haspass= $encoder->encodePassword($user,$user->getPlainPassword());
        $user->setPassword($haspass);
        
        
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

    /**
     * @Route("/api/edit", name="edit_user", methods={"POST"})
     */
    public function edit_user(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        $data = $request->getContent();
        $user = $serializer->deserialize($data,User::class,'json');
        $email=$user->getEmail();
        $pre_user=$manager->getRepository(User::class)->findOneBy(array("email"=>$email));
        
       

        $pre_user->setPassword($encoder->encodePassword($pre_user,$user->getPlainPassword()));

        $manager->flush();

        return new JsonResponse("modifié",Response::HTTP_OK,[],'json');



        // $hashpass= $encoder->encodePassword($user,$user->getPlainPassword());
        // $user->setPassword($hashpass);
        // $user->setPlainPassword("");
        
        // //gestion des erreurs de validation
        // $errors =  $validator->validate($user);
        // if(count($errors)){
        //     $errorJson = $serializer->serialize($errors,'json');
        //     return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);

        // }else{
        //     $manager->persist($user);
        //     $manager->flush();
        //     return new JsonResponse("ajouté",Response::HTTP_CREATED,[
        //     ],true);            
        // }
    }

}
