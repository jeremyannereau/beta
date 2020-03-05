<?php

namespace App\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    /**
     * @Route("/formation/creer_formation", name="creer_formation")
     */
    public function creer_formation(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    { $data=$request->getContent();
       $formation=$serializer->deserialize($data,Formation::class,"json");

       //gestion des erreurs de validation
     $errors =  $validator->validate($formation);
     if(count($errors)){
         $errorJson = $serializer->serialize($errors,'json');
         return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);

     }else{
         $manager->persist($formation);
         $manager->flush();
         return new JsonResponse("ajouté",Response::HTTP_CREATED,[
         ],true); 
         
        }
        
    }

    /**
     * @Route("/formation/modifier_formation", name="modifier_formation")
     */
    public function modifier_formation()
    {
        
    }

     /**
     * @Route("/formation/consulter_formation", name="consulter_formation")
     */
    public function consulter_formation()
    {
        
    }

     /**
     * @Route("/formation/supprimer_formation", name="supprimer_formation")
     */
    public function supprimer_formation()
    {
        
    }


}
