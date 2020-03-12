<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ContactController extends AbstractController
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
     * @Route("/contact/creer", name="creer_contact")
     */
    public function creer_contact(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    { 
        $data=$request->getContent();
        $contact=$serializer->deserialize($data,Contact::class,"json");
        $data=json_decode($data,true);
        $id_entreprise = $data["id_entreprise"];
        $entreprise=$manager->getRepository(Entreprise::class,'json')->findOneBy(array("id"=>$id_entreprise));
        $contact->setIdEntreprise($entreprise);
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
     * @Route("/contact/modifier", name="edit_contact")
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
     * @Route("/contact/consulter", name="consulter_contact")
     */
    public function consulter_contact(Request $request)
    {
        $data = $request->getContent();
        $contact = $this->serializer->deserialize($data,Contact::class,'json');
        $id=$contact->getId();
        $new_contact=$this->manager->getRepository(Contact::class,'json')->findOneBy(array("id"=>$id));
        if ($new_contact){
            $entreprise = $new_contact->getIdEntreprise();
            $entreprise=$this->manager->getRepository(Entreprise::class,'json')->findOneBy(array("id"=>$entreprise));
            
            $new_contact->setIdEntreprise($entreprise);
        
            $new_contact=$this->serializer->serialize($new_contact,'json',['groups'=>'contact']);
       
            return new JsonResponse($new_contact,Response::HTTP_OK,[],'json');
        }else{
            return new JsonResponse("Erreur de contact");
        }
        
    }
    /**
     * @Route("/contact/lister/entreprise", name="lister_contact_entreprise")
     */
    public function lister_contact_entreprise(Request $request)
    {
        $data=$request->getContent();
        $data=json_decode($data,true);
     
        $contacts = $this->manager->getRepository(Contact::class)->findBy(array("id_entreprise"=>$data["id"]));
        if ($contacts){
            $contacts = $this->serializer->serialize($contacts,'json',['groups'=>'contact']);
            return new JsonResponse($contacts,Response::HTTP_OK,[],true);
        }else{
            return new JsonResponse("Aucun contact dans cette entreprise pour le moment",Response::HTTP_OK,[],true);
        }
    }

    /**
     * @Route("/contact/supprimer", name="supprimer_contact")
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
