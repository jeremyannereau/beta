<?php

namespace App\Controller;



use App\Entity\User;
use App\Utilities\ApiFunctions;
use Doctrine\DBAL\DBALException;
use App\Repository\UserRepository;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

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
     * @Route("connect", name="connect_user", methods={"POST","GET"})
     */
    public function connect(Request $request) 
    {
        //Si erreur d'identifiant, l'Authenticator le gère en réponse 400
        $user=$this->getUser();
        
        // Si le header de la requête ne spécifie pas le format JSON
        if ($user==null){
            return new JsonResponse("Format de la requête incorrecte, format JSON demandé",Response::HTTP_BAD_REQUEST,[],'json');
        }else{

            // Si déja validé par un formateur ou un admin
            if (($user->getStatut()=="apprenant" | $user->getStatut()=="formateur" | $user->getStatut()=="admin" )){

                $utility = new ApiFunctions;

                $apiToken = $utility->genererToken($user);
                
                $user->setToken($apiToken); // je set un token au user
                
                $this->manager->flush($user);       // j'update le token du user

                return new JsonResponse([
                    'token' => $apiToken
                ]);

            // Si non = statut "inactif" en bdd ou bien erreur de bdd, pas de token renvoyé
            }else{
                return new JsonResponse("En attente d'activation",Response::HTTP_BAD_REQUEST,[],'json');
            }
        }    
    }

    /**
     * @Route("register", name="register_user", methods={"POST"})
     */
    public function register(Request $request,array $roles=array("ROLE_APPRENANT"))
    {
        try{
            $data = $request->getContent();
            $user = $this->serializer->deserialize($data,User::class,'json');
            $hashpass= $this->encoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($hashpass);
            $user->setStatut("inactif");
            $user->setRoles($roles);
            
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
        // Si l'utilisateur est existant, renvoi un message Json
        }catch(UniqueConstraintViolationException $e){
            //23000 = Not unique 
           if ($e->getSQLState()=="23000"){
                return new JsonResponse ("Utilisateur existant",Response::HTTP_BAD_REQUEST,[],true);
           }else{
               return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
           }
        }
        //Si le format de données n'est pas bon, par ex: integer au lieu de string
        catch(NotNormalizableValueException $e){
            return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
        }  
        //Si le format de données n'est pas bon, par ex: integer au lieu de string
        catch(NotEncodableValueException $e){
            return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
        }
    }

    /**
     * @Route("/api/edit", name="modifier_user", methods={"POST"})
     */
    public function api_edit(Request $request)
    {
        try{
            $data = $request->getContent();
            $user = $this->serializer->deserialize($data,User::class,'json');
            $token=$request->headers->get("X-AUTH-CONTENT");
            $pre_user=$this->manager->getRepository(User::class)->findOneBy(array("token"=>$token));
            
            if (($pre_user)!=null){
                
                $pre_user=$pre_user->setPassword($this->encoder->encodePassword($pre_user,$user->getPlainPassword()));
                $pre_user=$pre_user->setPhone($user->getPhone());      
                $this->manager->flush();
                return new JsonResponse("Utilisateur modifié",Response::HTTP_OK,[],'json'); 
            }else{
                // Si l'utilisateur n'existe pas, renvoi un message Json
                return new JsonResponse("Utilisateur inexistant",Response::HTTP_BAD_REQUEST,[],'json');
            }     
        }
        //Si le format de données n'est pas bon, par ex: integer au lieu de string
        catch(NotNormalizableValueException $e){
            return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
        }         
    }

    /**
     * @Route("/admin/valider/user", name="valider_user", methods={"POST"})
     */
    public function admin_valider_user(Request $request,$role="apprenant")
    {   
        //Vérif des droits suffisants à la validation d'un apprenant
        try{
            $token = $request->headers->get('X-AUTH-TOKEN');
            $demandeur=$this->manager->getRepository(User::class,'json')->findOneBy(array("token"=>$token));
            
            if ($role == "apprenant"){
    
                if ($demandeur == null | $demandeur->getStatut()!= ("admin" && "formateur")){
                    return new JsonResponse("Vous n'avez pas les droits d'accès1",Response::HTTP_UNAUTHORIZED,[],'json'); 
                }else{
                    $data = $request->getContent();
                    $user = $this->serializer->deserialize($data,User::class,'json'); 
                    $email=$user->getEmail();
                    $new_user=$this->manager->getRepository(User::class,'json')->findOneBy(array("email"=>$email));

                    if($new_user!=null){
                        //Validation du User   
                        $new_user=$new_user->setStatut($role);
                        $this->manager->flush();
                        return new JsonResponse($role . " validé",Response::HTTP_OK,[],'json');
                    }else{
                        return new JsonResponse("Utilisateur inexistant",Response::HTTP_BAD_REQUEST,[],'json');
                    }
                }
            }else if ($role =="formateur"){

                if ($demandeur->getStatut()!= ("admin")){
                    return new JsonResponse("Vous n'avez pas les droits d'accès2",Response::HTTP_UNAUTHORIZED,[],'json'); 
                }else{
                    $data = $request->getContent();
                    $user = $this->serializer->deserialize($data,User::class,'json'); 
                    $email=$user->getEmail();
                    $new_user=$this->manager->getRepository(User::class,'json')->findOneBy(array("email"=>$email));
                    if($new_user!=null){
                        //Validation du User   
                        $new_user=$new_user->setStatut($role);
                        $this->manager->flush();
                        return new JsonResponse($role ." validé",Response::HTTP_OK,[],'json');
                    }else{
                        return new JsonResponse("Utilisateur inexistant",Response::HTTP_BAD_REQUEST,[],'json');
                    }
                }
            }else{
                return new JsonResponse("Aucune validation possible",Response::HTTP_BAD_REQUEST,[],'json'); 
            }
        }//Si le format de données n'est pas bon, par ex: integer au lieu de string
        catch(NotNormalizableValueException $e){
            return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
        }  
        //Si le format de données n'est pas bon, par ex: integer au lieu de string
        catch(NotEncodableValueException $e){
            return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
        }

    }

    /**
     * @Route("/admin/supprimer/user", name="supprimer_user", methods={"POST"})
     */
    public function admin_supprimer_user(Request $request)
    {
        $token = $request->headers->get('X-AUTH-TOKEN');
        $demandeur=$this->manager->getRepository(User::class,'json')->findOneBy(array("token"=>$token));
        
        if ($demandeur == null | $demandeur->getStatut()!="admin"){
            return new JsonResponse("Vous n'avez pas les droits d'accès",Response::HTTP_UNAUTHORIZED,[],'json'); 
        }else{
            $data = $request->getContent();
            $user = $this->serializer->deserialize($data,User::class,'json');
            $email=$user->getEmail();
            $new_user=$this->manager->getRepository(User::class,'json')->findOneBy(array("email"=>$email));
            if ($new_user){
                $this->manager->remove($new_user);
                $this->manager->flush();             
                return new JsonResponse("User supprimé",Response::HTTP_OK,[],'json');  
            }else{
                return new JsonResponse("User inexistant",Response::HTTP_OK,[],'json');
            }  
        }
    }

    /**
     * @Route("/admin/consulter/user", name="consulter_user", methods={"POST"})
     */
    public function admin_consulter_user(Request $request)
    {
        try{
       
            $token = $request->headers->get('X-AUTH-TOKEN');
            $demandeur=$this->manager->getRepository(User::class,'json')->findOneBy(array("token"=>$token));
            
            if ($demandeur == null){
                return new JsonResponse("Vous n'avez pas les droits d'accès",Response::HTTP_UNAUTHORIZED,[],'json'); 
          
            }else if($demandeur->getStatut()!=("admin" && "formateur")){

                return new JsonResponse("Vous n'avez pas les droits d'accès",Response::HTTP_UNAUTHORIZED,[],'json'); 
            
            }else{
                $data = $request->getContent();
                $user = $this->serializer->deserialize($data,User::class,'json');
                
                $email=$user->getEmail();
                $user=$this->manager->getRepository(User::class,'json')->findOneBy(array("email"=>$email));
                $new_user=$this->manager->getRepository(User::class,'json')->findOneBy(array("email"=>$email));
               
                
                if ($new_user==null){
                    return new JsonResponse("Demande invalide",Response::HTTP_BAD_REQUEST,[],'json');    
                }   
                $role = $new_user->getStatut();


                if (($role == "formateur" && $demandeur->getStatut()!=("admin") && ($new_user->getId()!=$user->getId()))){
                    return new JsonResponse("Accès non autorisé",Response::HTTP_FORBIDDEN,[],'json');    
                }else{
                    $new_user=$this->serializer->serialize($new_user,'json',["groups"=>"user_profile"]);
                    return new JsonResponse($new_user,Response::HTTP_OK,[],'json');    
                }
            }
        }
    
        //Si le format de données n'est pas bon, par ex: integer au lieu de string
        catch(NotNormalizableValueException $e){
            return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
        }  
        //Si le format de données n'est pas bon, par ex: integer au lieu de string
        catch(NotEncodableValueException $e){
            return new JsonResponse (json_encode($e->getMessage()),Response::HTTP_BAD_REQUEST,[],true);
        }
    }
}
