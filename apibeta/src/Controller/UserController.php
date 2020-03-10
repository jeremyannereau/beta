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
    protected $manager;
    
    protected $serializer;
    protected $validator;
    protected $encoder;
        
    public function __construct(EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->encoder = $encoder;
    }

     /**
     * @Route("/api/connect", name="connect_user", methods={"POST","GET"})
     */
    public function api_connect(Request $request) 
    {
        $user=$this->getUser();
        // Si validé par un formateur ou admin
        if (!($user->getStatut()=="inactif")){

            $utility = new ApiFunctions;
            $apiToken = $utility->genererToken($user);
            
            $user->setToken($apiToken); // je set un token au user
            
            $this->manager->flush($user);       // j'update le token du user

            return new JsonResponse([
                'token' => $apiToken
            ]);
        // Si non, pas token 
        }else{
            return new JsonResponse("Attente activation",Response::HTTP_BAD_REQUEST,[],'json');
        }
    }

    /**
     * @Route("/api/register", name="register_user", methods={"POST"})
     */
    public function register_user(Request $request)
    {   // on recupere le contenu
        $data = $request->getContent(); 
        // on passe en objet le contenu
        $user = $this->serializer->deserialize($data,User::class,'json');
        // ?
        $hashpass= $this->encoder->encodePassword($user,$user->getPlainPassword());
        $user->setPassword($hashpass);
        $user->setStatut("inactif");
        
        //gestion des erreurs de validation
        $errors =  $this->validator->validate($user);
        if(count($errors)){
            $errorJson = $this->serializer->serialize($errors,'json');
            return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);

        }else{
            $this->manager->persist($user);
            $this->manager->flush();
            return new JsonResponse("ajouté",Response::HTTP_CREATED,[
            ],true);            
        }
    }

    /**
     * @Route("/api/edit", name="edit_user", methods={"POST"})
     */
    public function edit_user(Request $request)
    {
        $data = $request->getContent();

        $user = $this->serializer->deserialize($data,User::class,'json');

        $email=$user->getEmail();
        //recherche user par email
        $pre_user=$this->manager->getRepository(User::class)->findOneBy(array("email"=>$email));
       
        $pre_user=$pre_user->setPassword($this->encoder->encodePassword($pre_user,$user->getPlainPassword()));

        $pre_user=$pre_user->setPhone($user->getPhone());      
        
        $this->manager->flush();

        return new JsonResponse("Utilisateur modifié",Response::HTTP_OK,[],'json');        
    }

    /**
     * @Route("/admin/valider_user", name="valider_user", methods={"POST"})
     */
    public function valider_user(Request $request,$role="Apprenant")
    {
        $data = $request->getContent();
        $user = $this->serializer->deserialize($data,User::class,'json');
        
        $email=$user->getEmail();

        $new_user=$this->manager->getRepository(User::class,'json')->findOneBy(array("email"=>$email));
        
        $new_user=$new_user->setStatut($role);
      
        $this->manager->flush();

        return new JsonResponse($role . " validé",Response::HTTP_OK,[],'json');   
    }

    /**
     * @Route("/admin/supprimer_user", name="supprimer_user", methods={"POST"})
     */
    public function supprimer_user(Request $request)
    {
        $data = $request->getContent();
        $user = $this->serializer->deserialize($data,User::class,'json');
        $email=$user->getEmail();
        $new_user=$this->manager->getRepository(User::class,'json')->findOneBy(array("email"=>$email));
        
        $this->manager->remove($new_user);

        $this->manager->flush();
        
        
        return new JsonResponse("User supprimé",Response::HTTP_OK,[],'json');   
    }
}
