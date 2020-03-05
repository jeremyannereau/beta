<?php

namespace App\Controller;

use App\Entity\Candidature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatureController extends AbstractController
{
    /**
     * @Route("/candidature/creer_candidature", name="creer_candidature")
     */
    public function creer_candidature(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
     { $data=$request->getContent();
        $candidature=$serializer->deserialize($data,Candidature::class,"json");

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
    public function supprimer_candidature()
    {
       
    }



}
