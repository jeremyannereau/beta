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
     * @Route("/formation/creer", name="creer_formation")
     */
    public function creer_formation(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    { 
        $data=$request->getContent();
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
     * @Route("/formation/edit", name="edit_formation")
     */
    public function edit_formation(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
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
        return new JsonResponse("modifié",Response::HTTP_OK,[],'json');    
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
    public function supprimer_formation(Request $request)
    {
        $data = $request->getContent();
        $formation = $this->serializer->deserialize($data,Formation::class,'json');
        $id=$formation->getId();
        $new_formation=$this->manager->getRepository(Formation::class,'json')->findOneBy(array("id"=>$id));
        if ($new_formation){  
            $this->manager->remove($new_formation);
            $this->manager->flush();  
            return new JsonResponse("Formation supprimé",Response::HTTP_OK,[],'json');  
        }
        else{
            return new JsonResponse("Formation inexistant",Response::HTTP_OK,[],'json');
        }
    }
}