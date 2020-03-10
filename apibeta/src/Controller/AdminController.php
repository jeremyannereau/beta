<?php

namespace App\Controller;

use App\Entity\User;
use App\Controller\UserController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends UserController
{
    /**
     * @Route("/admin/valider/formateur", name="valider_formateur", methods={"POST"})
     */
    public function valider_formateur(Request $request)
    {
        return $this->admin_valider_user($request,"formateur");
    }
    /**
     * @Route("/admin/valider/apprenant", name="valider_apprenant", methods={"POST"})
     */
    public function valider_apprenant(Request $request)
    {
        return $this-> admin_valider_user($request,"apprenant");
    }
    /**
     * @Route("/admin/creer/formateur", name="creer_formateur", methods={"POST"})
     */
    public function creer_formateur(Request $request,$roles=array("ROLE_FORMATEUR","ROLE_APPRENANT"))
    {
        $this->register($request,$roles);
        $this->admin_valider_user($request,"formateur");
        return new JsonResponse("Formateur inscrit et validé",Response::HTTP_OK,[],'json');
    }
    /**
     * @Route("/admin/creer/apprenant", name="creer_apprenant", methods={"POST"})
     */
    public function creer_apprenant(Request $request)
    {
       $this->register($request);
       $this->admin_valider_user($request,"apprenant");
       return new JsonResponse("Apprenant inscrit et validé",Response::HTTP_OK,[],'json');
    }
    
    
    /**
     * @Route("/admin/consulter/user", name="consulter_user")
     */
    public function consulter_user(Request $request)
    {
        return $this->consulter_user($request);
    }
    /**
     * @Route("/admin/modifier/user", name="modifier_user")
     */
    public function modifier_user(Request $request)
    {
       return $this->modifier_user($request);
    }


    /**
     * @Route("/admin/modifier/formation", name="modifier_formation")
     */
    public function modifier_formation()
    {
       
    }
    /**
     * @Route("/admin/creer_groupe", name="creer_groupe")
     */
    public function creer_formation()
    {
       
    }
    /**
     * @Route("/admin/supprimer_groupe", name="supprimer_groupe")
     */
    public function supprimer_formation()
    {
       
    }

    /**
     * @Route("/admin/consulter_groupe", name="consulter_groupe")
     */
    public function consulter_formation()
    {
       
    }

    
     /**
     * @Route("/admin/consulter_board", name="consulter_board")
     */
    public function consulter_board()
    {
       
    }

     /**
     * @Route("/admin/list", name="list_users", methods={"POST"})
     */
    public function index(Request $request, UserRepository $repository,SerializerInterface $serializer) 
    {
        $token = $request->headers->get('X-AUTH-TOKEN');
        $demandeur=$this->manager->getRepository(User::class,'json')->findOneBy(array("token"=>$token));
        
        if ($demandeur == null | $demandeur->getStatut()!=("admin")){
            return new JsonResponse("Vous n'avez pas les droits d'accès",Response::HTTP_UNAUTHORIZED,[],'json'); 
        }else{

            $elements = $repository->findAll();
            $resultat = $serializer->serialize(
                $elements,
                'json',
                ["groups"=>"user_profile"]
            );
            return new JsonResponse($resultat,200,[],true);
        }
    }

   
}
