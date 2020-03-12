<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FormationController extends AbstractController
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
                $formation = $this->serializer->serialize($formation,'json',[]);
                return new JsonResponse($formation,Response::HTTP_OK,[],true);
            }else if ($statut=="formateur"){
                $formation = $this->manager->getRepository(Formation::class)->findBy(array(""));
                $formation = $this->serializer->serialize($formation,'json',[]);
                return new JsonResponse($formation,Response::HTTP_OK,[],true);
            }else{
                return new JsonResponse("Vous n'avez pas les droits1",Response::HTTP_UNAUTHORIZED,[],'json'); 
            }
        }else{
            return new JsonResponse("Vous n'avez pas les droits2",Response::HTTP_UNAUTHORIZED,[],'json');
        }
        
        
    }
    /**
     * @Route("/formation/creer", name="creer_formation")
     */
    public function creer_formation(Request $request,$user)
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
                $this->lier_formateur_formation($formation,$user);          
            }
            return new JsonResponse("Formation ajoutée",Response::HTTP_CREATED,[
            ],true);            
        }
    }

    /**
     * @Route("/formation/lier/formateur", name="lier_formateur_formation")
     */
    public function lier_formateur_formation($formation, $user)
    {
        $id_formation=$formation->getId();
        $id_user=$user->getId();

        
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
    public function consulter_formation()
    {
        $data = $this->request->getContent();
        $formation = $this->serializer->deserialize($data,Formation::class,'json');
        $id=$formation->getId();
        $new_formation=$this->manager->getRepository(formation::class,'json')->findOneBy(array("id"=>$id));
        $new_formation=$this->serializer->serialize($new_formation,'json',[]);

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

    


}

