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
          dd($contact);   
              
      }

    }

    /**
     * @Route("/contact/modifier_contact", name="modifier_contact")
     */
    public function modifier_contact()
    {
        
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
    public function supprimer_contact()
    {
        
    }
}
