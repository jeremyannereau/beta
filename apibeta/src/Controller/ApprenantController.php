<?php

namespace App\Controller;

use App\Entity\Candidature;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApprenantController extends AbstractController
{
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
