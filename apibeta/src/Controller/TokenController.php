<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class TokenController extends AbstractController{
    /**
     * @Route("verifToken", name="verif_token", methods={"POST"})
     */
    public function verifToken(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager){
    
            $data = $request->getContent();
            $user = $serializer->deserialize($data,User::class,'json');
            
            if ($token = $user->getToken()){
                $token = $user->getToken();
            }else{
                return new JsonResponse (json_encode(false),Response::HTTP_OK,[],true);
            }
           
            $pre_user=$manager->getRepository(User::class)->findOneBy(array("token"=>$token));
        
            if ($pre_user){
                $statut= $pre_user->getstatut();
                return new JsonResponse (json_encode($statut),Response::HTTP_OK,[],true);
            }else{
                return new JsonResponse (json_encode(false),Response::HTTP_OK,[],true);
            }
       
    }
}