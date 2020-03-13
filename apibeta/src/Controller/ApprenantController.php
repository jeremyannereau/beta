<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Candidature;
use App\Controller\FormateurController;
use App\Controller\FormationController;
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
use Symfony\Component\Serializer\Encoder\JsonDecode;

class ApprenantController extends AbstractController
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
     * @Route("apprenant/ajouter/formation", name="apprenant_ajouter_formation")
     */
    public function ajouter_formation(Request $request)
    {
        $token=$request->headers->get("X-AUTH-CONTENT");
        
        $user=$this->manager->getRepository(User::class)->findOneBy(array("token"=>$token));

        if ($user){
            $statut=$user->getStatut();
            
            if ($statut=="apprenant"){
               
                $id_formation=$request->getContent();
                
                $id_formation=json_decode($id_formation,"json");
                $id_formation=$id_formation["id"];
                
                $formation=$this->manager->getRepository(Formation::class)->findOneBy(array("id"=>$id_formation));
                
                $controller = new FormationController($this->manager,$this->serializer,$this->validator,$this->encoder);
                                
                return $controller->lier_formation($formation,$user);
                //return new JsonResponse("Je travaille dessus",Response::HTTP_OK,[],'json');
            }else{
                return new JsonResponse("Vous n'avez pas les droits1",Response::HTTP_UNAUTHORIZED,[],'json');
            }
        }else{
            return new JsonResponse("Vous n'avez pas les droits2",Response::HTTP_UNAUTHORIZED,[],'json');
        } 
    }
    /**
     * @Route("/apprenant/recherche_entreprise", name="recherche_entreprise")
     */
    public function recherche_entreprise()
    {
      
    }
    /**
     * @Route("/apprenant/poster_candidature", name="poster_candidature")
     */
    public function poster_candidature()
    {
      
    }
    /**
     * @Route("/apprenant/consulter_candidature", name="consulter_candidature")
     */
    public function consulter_candidature()
    {
      
    }
    /**
     * @Route("/apprenant/modifier_candidature", name="modifier_candidature")
     */
    public function modifier_candidature()
    {   
      
    }
    /**
     * @Route("/apprenant/supprimer_candidature", name="supprimer_candidature")
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
        }
        else{
            return new JsonResponse("Candidature inexistant",Response::HTTP_OK,[],'json');
        }
}
    /**
     * @Route("/apprenant/afficher_profil", name="afficher_profil")
     */
    public function afficher_profil()
    {   
      
    }
    /**
     * @Route("/apprenant/modifier_profil", name="modifier_profil")
     */
    public function modifier_profil()
    {   
      
    }   
    /**
     * @Route("/apprenant/supprimer_profil", name="supprimer_profil")
     */
    public function supprimer_profil()
    {   
      
    }
}
