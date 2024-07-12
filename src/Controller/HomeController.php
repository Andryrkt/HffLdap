<?php

namespace App\Controller;



use App\Service\InformixService;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    
    /**
     * @Route("/home", name="home_index")
     */
    public function index(Security $security): Response
    {

       // Récupérer l'utilisateur connecté
       $user = $security->getUser();

       // Afficher les informations de l'utilisateur pour le débogage
       dd($user);

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
