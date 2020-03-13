<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Formation;
use App\Entity\FormationUsers;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonSerializable;
use Normalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FormationController extends AbstractController implements JsonSerializable
{
    protected $manager;
    protected $serializer;
    protected $validator;
    protected $encoder;
    protected $normalizer;

    public function __construct(EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    { 
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->encoder = $encoder;
    

    }
    /**
     * @Route("/formation/lister", name="lister_formation")
     */
    public function lister_formation(Request $request)
    { 
        $token=$request->headers->get("X-AUTH-CONTENT");
        
        $user=$this->manager->getRepository(User::class)->findOneBy(array("token"=>$token));

        if ($user){
            $statut=$user->getStatut();
            
            if ($statut=="admin"){
                $formation = $this->manager->getRepository(Formation::class)->findAll();
                $formation = $this->serializer->serialize($formation,'json',['groups'=>'simple']);
                return new JsonResponse($formation,Response::HTTP_OK,[],true);
            }else if ($statut=="formateur"){
                $formation = $this->manager->getRepository(Formation::class)->findBy(array(""));
                $formation = $this->serializer->serialize($formation,'json',['groups'=>'simple']);
                return new JsonResponse($formation,Response::HTTP_OK,[],true);
            }else{
                return new JsonResponse("Vous n'avez pas les droits1",Response::HTTP_UNAUTHORIZED,[],'json'); 
            }
        }else{
            return new JsonResponse("Vous n'avez pas les droits2",Response::HTTP_UNAUTHORIZED,[],'json');
        }   
    }

    /**
     * @Route("/formation/rechercher/criteres", name="rechercher_formation_criteres")
     */
    public function rechercher_formation_criteres (EntityManagerInterface $manager, SerializerInterface $serializer, Request $request, FormationRepository $repository){
        
            $data=$request->getContent();
            $recherche = $this->serializer->deserialize($data,Formation::class,"json");
            //dd($recherche);
            $nom = $recherche->getNom();
            
            $tags= $recherche->getTags();
        
            
            $formation = $repository->findBySelect($nom, $tags);
            
            $formation = $this->serializer->serialize($formation,'json',['groups'=>'simple']);
            
            return new JsonResponse(($formation),Response::HTTP_OK,[],true);
        
        
    }





    /**
     * @Route("/formation/lister/user", name="lister_formation_user")
     */
    public function lister_formation_user(Request $request)
    { 
        $token=$request->headers->get("X-AUTH-CONTENT");
        $formation=$request->getContent();
        $formation = json_decode($formation,true);
        
        $user=$this->manager->getRepository(User::class)->findOneBy(array("token"=>$token));
        
        
        if ($user){
            //dd($user);
            $statut=$user->getStatut(); 
            if ($statut=="admin"){
                $formations=$this->manager->getRepository(Formation::class)->findAll();
               // $formations=$this->objectToArray($formations);   
                dd(($formations));
                return new JsonResponse("",Response::HTTP_OK,[],true);
            }else if ($statut=="formateur"){
                $formation=$this->manager->getRepository(Formation::class)->findOneBy(array("id"=>$formation["id"]));
                return new JsonResponse($formation,Response::HTTP_OK,[],true);
            }else{
            return new JsonResponse("Vous n'avez pas les droits2",Response::HTTP_UNAUTHORIZED,[],'json');
        }
    }
}
    /**
     * @Route("/formation/creer", name="creer_formation")
     */
    public function creer_formation(Request $request,$user=null)
    { 
        $data=$request->getContent();
        $formation=$this->serializer->deserialize($data,Formation::class,"json");

        //gestion des erreurs de validation
        $errors =  $this->validator->validate($formation);
        if(count($errors)){
            $errorJson = $this->serializer->serialize($errors,'json');
            return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);
        }else{
            $this->manager->persist($formation);
            $this->manager->flush();
            
            if ($user){
                $this->lier_formation($formation,$user); 
                return new JsonResponse("Formation ajoutée3",Response::HTTP_CREATED,[
                ],true);         
            }
            return new JsonResponse("Formation ajoutée",Response::HTTP_CREATED,[
            ],true);            
        }
    }
    /**
     * @Route("/formation/lier/formateur", name="lier_formateur_formation")
     */
    public function lier_formation($formation, $user)
    {
        $entity = new FormationUsers();
        $entity->addIdFormation($formation);
        $entity->addIdUser($user);
        $this->manager->persist($entity);
        $this->manager->flush($entity);
        return new JsonResponse("ok",Response::HTTP_OK,[],'json');
    }
    
    /**
     * @Route("/formation/modifier", name="modifier_formation")
     */
    public function modifier_formation(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data = $request->getContent();
        $formation = $serializer->deserialize($data,Formation::class,'json');
        $id=$formation->getId();
        $pre_formation=$manager->getRepository(Formation::class)->findOneBy(array("id"=>$id));
        $pre_formation=$pre_formation->setNom($formation->getNom());
        $pre_formation=$pre_formation->setStatut($formation->getStatut());
        $pre_formation=$pre_formation->setTags($formation->getTags());    
        $pre_formation=$pre_formation->setDateDebut($formation->getDateDebut());
        $pre_formation=$pre_formation->setDateFin($formation->getDateFin());
        $manager->flush();
        return new JsonResponse("Formation modifiée",Response::HTTP_OK,[],'json');    
    }
    /**
    * @Route("/formation/consulter", name="consulter_formation")
    */
    public function consulter_formation(Request $request)
    {
        $data = $request->getContent();
        $formation = $this->serializer->deserialize($data,Formation::class,'json');
        $id=$formation->getId();
        $new_formation=$this->manager->getRepository(formation::class,'json')->findOneBy(array("id"=>$id));
        $new_formation=$this->serializer->serialize($new_formation,'json',['groups'=>'simple']);

        return new JsonResponse($new_formation,Response::HTTP_OK,[],'json');
    }

     /**
     * @Route("/formation/supprimer_formation", name="supprimer_formation")
     */
    public function supprimer_formation(Request $request)
    {
        $data = $request->getContent();
        $formation = $this->serializer->deserialize($data,Formation::class,'json');
        $id=$formation->getId();
        $new_formation=$this->manager->getRepository(Formation::class,'json')->findOneBy(array("id"=>$id));
        if ($new_formation){  
            $this->manager->remove($new_formation);
            $this->manager->flush();  
            return new JsonResponse("Formation supprimée",Response::HTTP_OK,[],'json');  
        }
        else{
            return new JsonResponse("Formation inexistante",Response::HTTP_OK,[],'json');
        }
    }
    public function jsonSerialize()
    {
        
    }

    function objectToArray ($object) {
        if(!is_object($object) && !is_array($object))
            return $object;
        return array_map('objectToArray', (array) $object);
    }
}

