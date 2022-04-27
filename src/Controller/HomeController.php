<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Parcour;
use App\Form\ParcourType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('app_login');
    }
    

    

    //Permet d'ajouter un user avec mot de passe crypté
    /** 
     * @IsGranted("ROLE_super_admin")
     * @Route("/login/AjouterCompte", name="ajouterCompte")
     **/
    public function addCompte(Request $request): Response
    { 
	    $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        
        $encoded = password_hash("P@ssw0rd",PASSWORD_BCRYPT);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($encoded);
        $user->setUsername("admin");
	    $entityManager->persist($user);
        $entityManager->flush(); 
       
        return $this->redirectToRoute('app_login');
        
    }

   
    /**
     * @Route("/menuJSON", name="menuJSON")
     */
    public function menuJSON(): JsonResponse
    {
        
        /*if($_GET['type']=="user"){
            $items=$this->getDoctrine()->getRepository(User::class)->findAll();
        }
        elseif($_GET['type']=='parcour'){
            $items=$this->getDoctrine()->getRepository(Parcour::class)->findAll();
        }
        elseif($_GET['type']=='acteur'){
            $items=$this->getDoctrine()->getRepository(Acteur::class)->findAll();
        }
        else{
            $items=$this->getDoctrine()->getRepository(Personnage::class)->findAll();
        }*/
        
        $items=$this->getDoctrine()->getRepository(Parcour::class)->findAll();
        $data = [];
        foreach($items as $unItem){
            $data[]=$unItem->dataJson();
        }
        
        dump($data);
        return new JsonResponse($data, Response::HTTP_OK);

       
    }

     /**
     * @Route("/test", name="test")
     */
    public function test(Request $request): Response
    { 
        
        $items=$this->getDoctrine()->getRepository(Parcour::class)->findAll();
        $data = [];
        foreach($items as $unItem){
            $data[]=$unItem->dataJson();
        }
        
        dump($data);
        return $this->render('test.html.twig', [
            
        ]);
        
        
    }


}
