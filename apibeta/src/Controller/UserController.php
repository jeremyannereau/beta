<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("admin/list", name="list_users", methods={"POST"})
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
