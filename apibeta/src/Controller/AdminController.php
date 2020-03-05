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
     * @Route("/admin/creer_formateur", name="creer_formateur")
     */
    public function creer_formateur(Request $request)
    {
        $this->register_user($request);
        $this->valider_user($request,"formateur");
        return new JsonResponse("Formateur inscrit et validé",Response::HTTP_OK,[],'json');
    }

    /**
     * @Route("/admin/supprimer_user", name="supprimer_user")
     */
    public function supprimer_user(Request $request)
    {
       return $this->supprimer_user($request);
    }

    /**
     * @Route("/admin/consulter_formateur", name="consulter_formateur")
     */
    public function consulter_formateur()
    {
        
    }

     /**
     * @Route("/admin/valider_formateur", name="valider_formateur")
     */
    public function valider_formateur(Request $request)
    {
        return $this->valider_user($request,"formateur");
    }

     /**
     * @Route("/admin/modifier_formateur", name="modifier_formateur")
     */
    public function modifier_formateur()
    {
       
    }

     /**
     * @Route("/formateur/valider_apprenant", name="valider_apprenant", methods={"POST"})
     */
    public function valider_apprenant(Request $request)
    {
        return $this-> valider_user($request,"apprenant");
    }

    /**
     * @Route("/admin/creer_apprenant", name="creer_apprenant")
     */
    public function creer_apprenant(Request $request)
    {
       $this->register_user($request);
       $this->valider_user($request,"apprenant");
       return new JsonResponse("Apprenant inscrit et validé",Response::HTTP_OK,[],'json');
    }

    /**
     * @Route("/admin/consulter_apprenant", name="consulter_apprenant")
     */
    public function consulter_apprenant()
    {
       
    }

     /**
     * @Route("/admin/supprimer_apprenant", name="supprimer_apprenant")
     */
    public function supprimer_apprenant()
    {
       
    }

    /**
     * @Route("/admin/modifier_apprenant", name="modifier_apprenant")
     */
    public function modifier_apprenant(Request $request)
    {
       return $this->edit_user($request);
    }

     /**
     * @Route("/admin/modifier_groupe", name="modifier_groupe")
     */
    public function modifier_groupe()
    {
       
    }

     /**
     * @Route("/admin/creer_groupe", name="creer_groupe")
     */
    public function creer_groupe()
    {
       
    }

    /**
     * @Route("/admin/supprimer_groupe", name="supprimer_groupe")
     */
    public function supprimer_groupe()
    {
       
    }

     /**
     * @Route("/admin/consulter_groupe", name="consulter_groupe")
     */
    public function consulter_groupe()
    {
       
    }

    
     /**
     * @Route("/admin/consulter_board", name="consulter_board")
     */
    public function consulter_board()
    {
       
    }

     /**
     * @Route("/admin/list", name="list_users", methods={"GET"})
     */
    public function index(UserRepository $repository,SerializerInterface $serializer) 
    {
        $elements = $repository->findAll();
        $resultat = $serializer->serialize(
            $elements,
            'json',
            [
            ]
        );
        return new JsonResponse($resultat,200,[],true);
    }

   
}
