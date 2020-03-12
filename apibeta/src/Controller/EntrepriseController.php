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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\EntrepriseRepository;
class EntrepriseController extends AbstractController
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
     * @Route("/entreprise/lister", name="lister_entreprise")
     */
    public function lister_entreprise (EntityManagerInterface $manager, SerializerInterface $serializer){

        $entreprises = $manager->getRepository(Entreprise::class)->findAll();
        $entreprises = $serializer->serialize($entreprises,'json',['groups'=>'entreprise_candidature']);
        return new JsonResponse($entreprises,Response::HTTP_OK,[],true);
    }
    
    /**
     * @Route("/entreprise/rechercher/secteurs", name="rechercher_secteurs")
     */
    public function rechercher_secteurs(EntityManagerInterface $manager, SerializerInterface $serializer, Request $request, EntrepriseRepository $repository){
        //Renvoie les secteurs de la BDD
        $entreprises = $manager->getRepository(Entreprise::class)->findAll();
        
        $secteurs = [];
       
        foreach($entreprises as $entreprise){

            if ($entreprise->getSecteur()!=""){
                $secteurs[] = $entreprise->getSecteur();
            } 
        }
        return new JsonResponse(json_encode($secteurs),Response::HTTP_OK,[],true);

    }
     /**
     * @Route("/entreprise/rechercher/departements", name="rechercher_departements")
     */
    public function rechercher_departements(EntityManagerInterface $manager, SerializerInterface $serializer, Request $request, EntrepriseRepository $repository){
        //Renvoie les départemnts de la BDD
        $entreprises = $manager->getRepository(Entreprise::class)->findAll();
        
        $departements = [];

        foreach($entreprises as $entreprise){

            if ($entreprise->getDepartement()!=""){
                $departements[] = $entreprise->getDepartement();
            }
        }
        return new JsonResponse(json_encode($departements),Response::HTTP_OK,[],true);

    }
    /**
     * @Route("/entreprise/rechercher/criteres", name="rechercher_entreprise_criteres")
     */
    public function rechercher_entreprise_criteres (EntityManagerInterface $manager, SerializerInterface $serializer, Request $request, EntrepriseRepository $repository){
        
        $data=$request->getContent();
        $recherche = $serializer->deserialize($data,Entreprise::class,"json");
        
        $nom = $recherche->getNom();
        $secteur=$recherche->getSecteur();
        $departement=$recherche->getDepartement();
        $ville=$recherche->getVille();
        $adresse=$recherche->getAdresse();
        
        $entreprises = $repository->findBySelect($nom,$secteur,$departement,$ville,$adresse);

        $entreprises = $serializer->serialize($entreprises,'json');

        return new JsonResponse(($entreprises),Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/entreprise/creer", name="creer_entreprise")
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
     * @Route("/entreprise/edit", name="edit_entreprise")
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
     * @Route("/entreprise/consulter", name="consulter_entreprise")
     */
    public function consulter_entreprise(Request $request)
    {
        $data = $request->getContent();
        $entreprise = $this->serializer->deserialize($data,Entreprise::class,'json');
        $id=$entreprise->getId();
        $new_entreprise=$this->manager->getRepository(Entreprise::class,'json')->findOneBy(array("id"=>$id));
        $new_entreprise=$this->serializer->serialize($new_entreprise,'json',[]);

        return new JsonResponse($new_entreprise,Response::HTTP_OK,[],'json');
    }

    /**
     * @Route("/entreprise/supprimer", name="supprimer_entreprise")
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
            
            return new JsonResponse("Entreprise supprimée",Response::HTTP_OK,[],'json');  
        }else{
            return new JsonResponse("Entreprise inexistante",Response::HTTP_OK,[],'json');
        }
    }
}
