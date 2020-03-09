<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact/creer_contact", name="creer_contact")
     */
    public function creer_contact(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    { $data=$request->getContent();
        $contact=$serializer->deserialize($data,Contact::class,"json");

        //gestion des erreurs de validation
      $errors =  $validator->validate($contact);
      if(count($errors)){
          $errorJson = $serializer->serialize($errors,'json');
          return new JsonResponse($errorJson,Response::HTTP_BAD_REQUEST,[],true);

      }else{
          $manager->persist($contact);
          $manager->flush();
          return new JsonResponse("ajouté",Response::HTTP_CREATED,[
          ],true); 
             
              
      }

    }

    /**
     * @Route("/contact/edit_contact", name="edit_contact")
     */
    public function edit_contact(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data = $request->getContent();
        

      
        $contact = $serializer->deserialize($data,Contact::class,'json');

        
        $id=$contact->getId();
       

        $pre_contact=$manager->getRepository(Contact::class)->findOneBy(array("id"=>$id));
        
       
        $pre_contact=$pre_contact->setNom($contact->getNom());

        $pre_contact=$pre_contact->setprenom($contact->getprenom());

        $pre_contact=$pre_contact->setTelephone($contact->getTelephone());    
        
        $pre_contact=$pre_contact->setposte($contact->getposte());

        $pre_contact=$pre_contact->setemail($contact->getemail());

        
        $manager->flush();

        return new JsonResponse("modifié",Response::HTTP_OK,[],'json');  
       dd($contact);
    }


    /**
     * @Route("/contact/consulter_contact", name="consulter_contact")
     */
    public function consulter_contact()
    {
        
    }

    /**
     * @Route("/contact/supprimer_contact", name="supprimer_contact")
     */

    public function supprimer_contact(Request $request)
    {
        $data = $request->getContent();
        $contact = $this->serializer->deserialize($data,Contact::class,'json');
        $id=$contact->getId();
        $new_contact=$this->manager->getRepository(Contact::class,'json')->findOneBy(array("id"=>$id));
        if ($new_contact){
            $this->manager->remove($new_contact);

            $this->manager->flush();
            
            
            return new JsonResponse("Contact supprimé",Response::HTTP_OK,[],'json');  
        }else{
            return new JsonResponse("Contact inexistant",Response::HTTP_OK,[],'json');
        }
         
    }
}
