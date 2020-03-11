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

        dd($candidature);

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
     * @Route("/candidature/consulter_candidature", name="consulter_candidature")
     */
    public function consulter_candidature()
    {
       
    }

    /**
     * @Route("/candidature/modifier_candidature", name="modifier_candidature")
     */
    public function modifier_candidature()
    {
       
    }

    /**
     * @Route("/candidature/supprimer_candidature", name="supprimer_candidature")
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
            
            
            return new JsonResponse("Candidature supprimé",Response::HTTP_OK,[],'json');  
        }else{
            return new JsonResponse("Candidature inexistant",Response::HTTP_OK,[],'json');
        }
         
    }



}
