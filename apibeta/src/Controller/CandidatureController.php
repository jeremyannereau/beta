<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Entreprise;
use App\Entity\Candidature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Encoder\EncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface as EncoderEncoderInterface;

class CandidatureController extends AbstractController
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
     * @Route("/candidatures/lister", name="lister_candidatures")
     */
    public function lister_candidature ()
    {

        $candidatures = $this->manager->getRepository(Candidature::class)->findAll();
        $candidatures = $this->serializer->serialize($candidatures,'json',['groups'=>'nomansland']);
        return new JsonResponse($candidatures,Response::HTTP_OK,[],true);
    }
     /**
     * @Route("/candidatures/lister/token", name="lister_candidatures_token")
     */
    public function lister_cand (Request $request){
        $data=$request->getContent();
        $data=json_decode($data,true);
        
        $user=$this->manager->getRepository(User::class)->findBy(array("token"=>$data["token"]));
        
        $iduser=$user[0]->getId();
        
        $entreprises = $this->manager->getRepository(Candidature::class)->findBy(array("id_user"=>$iduser));
        
        $entreprises = $this->serializer->serialize($entreprises,'json',['groups'=>'nomansland']);
        return new JsonResponse($entreprises,Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/candidature/creer", name="creer_candidature")
     */
    public function creer_candidature(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer,EncoderEncoderInterface $encoder, ValidatorInterface $validator)
    { 
        $data = $request->getContent();
        $candidature=$serializer->deserialize($data,Candidature::class,"json");
       
        $data=json_decode($data,true); 
        
        $id_user = $data["id_user"];
        $id_entreprise = $data["id_entreprise"];

        $entreprise=$manager->getRepository(Entreprise::class,'json')->findOneBy(array("id"=>$id_entreprise));
        $user=$manager->getRepository(User::class,'json')->findOneBy(array("id"=>$id_user));

        $candidature->setIdEntreprise($entreprise);
        $candidature->setIdUser($user);

        //gestion des erreurs de validation
        $errors =  $validator->validate($candidature);

        if(count($errors)){
            $errorJson = $serializer->serialize($errors,'json');
            return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);
        }else{
            $manager->persist($candidature);
            $manager->flush();
            return new JsonResponse("ajouté",Response::HTTP_CREATED,[
            ],true); 
        }   
    }

    /**
     * @Route("/candidature/consulter", name="consulter_candidature")
     */
    public function consulter_candidature(Request $request)
    {
        $data = $request->getContent();
        
        $candidature = $this->serializer->deserialize($data,Candidature::class,'json');
     
        $id=$candidature->getId();
        
        $new_candidature=$this->manager->getRepository(Candidature::class,'json')->findOneBy(array("id"=>$id));
        $entreprise = $new_candidature->getIdEntreprise();
        $user = $new_candidature->getIdUser();
        
        $entreprise=$this->manager->getRepository(Entreprise::class,'json')->findOneBy(array("id"=>$entreprise));
        //dd($entreprise);
        $user=$this->manager->getRepository(User::class,'json')->findOneBy(array("id"=>$user));
        //dd($user);
    
        $new_candidature->setIdEntreprise($entreprise);
        $new_candidature->setIdUser($user);
        $new_candidature=$this->serializer->serialize($new_candidature,'json',['groups'=>'nomansland']);
        return new JsonResponse($new_candidature,Response::HTTP_OK,[],'json');
    }

    /**
     * @Route("/candidature/modifier", name="modifier_candidature")
     */
    public function modifier_candidature(Request $request)
    {
       $data=$request->getContent();
       
       $candidature = $this->serializer->deserialize($data,Candidature::class,'json');
       $id=$candidature->getId();
    
       $pre_candidature=$this->manager->getRepository(Candidature::class)->findOneBy(array("id"=>$id));
      // dd($pre_candidature);
       $pre_formation=$pre_candidature->setReponse($candidature->getReponse());
       
       $this->manager->flush();
       return new JsonResponse("modifié",Response::HTTP_OK,[],'json');  
    }

    /**
     * @Route("/candidature/supprimer", name="supprimer_candidature")
     */
    public function supprimer_candidature(Request $request)
    {
        $data = $request->getContent();
        $candidature = $this->serializer->deserialize($data,Candidature::class,'json');
        $id=$candidature->getId();
        $new_candidature=$this->manager->getRepository(Candidature::class,'json')->findOneBy(array("id"=>$id));
        if ($new_candidature){
            $this->manager->remove($new_candidature);
            $this->manager->flush();
            return new JsonResponse("Candidature supprimée",Response::HTTP_OK,[],'json');  
        }else{
            return new JsonResponse("Candidature inexistante",Response::HTTP_OK,[],'json');
        }         
    }
}
