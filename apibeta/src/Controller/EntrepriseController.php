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
     * @Route("/entreprise/edit_entreprise", name="edit_entreprise")
     */
    public function edit_entreprise(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data = $request->getContent();

      
        $entreprise = $serializer->deserialize($data,Entreprise::class,'json');
        $id=$entreprise->getId();
     

        $pre_entreprise=$manager->getRepository(Entreprise::class)->findOneBy(array("id"=>$id));
        
       
        $pre_entreprise=$pre_entreprise->setnom($entreprise->getnom());

        $pre_entreprise=$pre_entreprise->setTelephone($entreprise->getTelephone());    
        
        $pre_entreprise=$pre_entreprise->setadresse($entreprise->getadresse());
        
        $manager->flush();

        return new JsonResponse("modifié",Response::HTTP_OK,[],'json');  
       
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

    public function supprimer_entreprise(Request $request)
    {
        $data = $request->getContent();
        $entreprise = $this->serializer->deserialize($data,Entreprise::class,'json');
        $id=$entreprise->getId();
        $new_entreprise=$this->manager->getRepository(Entreprise::class,'json')->findOneBy(array("id"=>$id));
        if ($new_entreprise){

            
            $this->manager->remove($new_entreprise);

            $this->manager->flush();
            
            
            return new JsonResponse("Entreprise supprimé",Response::HTTP_OK,[],'json');  
        }else{
            return new JsonResponse("Entreprise inexistant",Response::HTTP_OK,[],'json');
        }
         
    }
}
