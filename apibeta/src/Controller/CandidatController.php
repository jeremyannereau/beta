<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CandidatController extends AbstractController
{
    /**
     * @Route("/candidat", name="candidat")
     */
    public function index()
    {
        return $this->render('candidat/index.html.twig', [
            'controller_name' => 'CandidatController',
        ]);
    }
}
