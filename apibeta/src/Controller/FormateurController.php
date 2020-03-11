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

class FormateurController extends AbstractController
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
     * @Route("/formateur/creer_groupe", name="creer_groupe")
     */
    public function creer_groupe()
    {
     
    }

    /**
     * @Route("/formateur/modifier_groupe", name="modifier_groupe")
     */
    public function modifier_groupe()
    {
     
    }

     /**
     * @Route("/formateur/supprimer/groupe", name=" supprimer_groupe")
     */
    public function supprimer_groupe(Request $request)
    {
        $data = $request->getContent();
        $groupe = $this->serializer->deserialize($data,Formation::class,'json');
        $id=$groupe->getId();
        $new_groupe=$this->manager->getRepository(Formation::class,'json')->findOneBy(array("id"=>$id));
        if ($new_groupe){

            
            $this->manager->remove($new_groupe);

            $this->manager->flush();
            
            
            return new JsonResponse("Groupe supprimé",Response::HTTP_OK,[],'json');  
        }
        else{
            return new JsonResponse("Groupe inexistant",Response::HTTP_OK,[],'json');
        }
         
    }

    /**
     * @Route("/formateur/ valider_apprenant", name=" valider_apprenant")
     */
    public function valider_apprenant()
    {
     
    }

    /**
     * @Route("/formateur/ supprimer_apprenant", name=" supprimer_apprenant")
     */
    public function supprimer_apprenant()
    {
     
    }

    /**
     * @Route("/formateur/afficher_board", name="afficher_board")
     */
    public function afficher_board()
    {
     
    }

     /**
     * @Route("/formateur/consulter_apprenant", name="consulter_apprenant")
     */
    public function consulter_apprenant()
    {
     
    }

   

}
