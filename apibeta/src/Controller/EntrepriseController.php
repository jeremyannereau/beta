<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprise/creer_entreprise", name="creer_entreprise")
     */
    public function creer_entreprise(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator )
    {
      $data=$request->getContent();
      $entreprise=$serializer->deserialize($data,Entreprise::class,"json");
    

      //gestion des erreurs de validation
      $errors =  $validator->validate($entreprise);
      if(count($errors)){
          $errorJson = $serializer->serialize($errors,'json');
          return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);

      }else{
          $manager->persist($entreprise);
          $manager->flush();
          return new JsonResponse("ajouté",Response::HTTP_CREATED,[
          ],true); 
               
      }
        
   
    }
    

     /**
     * @Route("/entreprise/modifier_entreprise", name="modifier_entreprise")
     */
    public function modifier_entreprise()
    {
      
    }

    /**
     * @Route("/entreprise/consulter_entreprise", name="consulter_entreprise")
     */
    public function consulter_entreprise()
    {
      
    }

    /**
     * @Route("/entreprise/supprimer_entreprise", name="supprimer_entreprise")
     */
    public function supprimer_entreprise()
    {
      
    }
}
