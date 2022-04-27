<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Parcour;
use App\Form\ParcourType;
use Symfony\Component\Validator\Constraints\DateTime;
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

   
    //Route du menu des parcours
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/menu", name="menu")
     */
    public function menu(): Response
    {
        $parcours= $this->getDoctrine()->getRepository(Parcour::class)->findOneByUserId($this->getUser()->getId());       
        
        return $this->render('menu.html.twig',['parcours'=>$parcours]);
    }

    //Permet d'ajouter un parcour
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/addparcour", name="addparcour")
     */
    public function addparcour(Request $request): Response
    {
        $em= $this->getDoctrine()->getManager();
        $parcour= new Parcour();
        $form= $this->createForm(ParcourType::class, $parcour);
        $form->handleRequest($request);
        
        
        if($form->isSubmitted() && $form->isValid()){
           
            $heureDebut=$parcour->getHeureDebut();
            $now= new \DateTime();

            $duree=new \DateTime($now->diff($heureDebut)->format("%H:%i:%s"));
            $array_duree=explode(":",$duree->format("H:i:s"));//Remplis un tableau d'entier avec les heure, minutes, secondes... 
            $secondes=60*intval($array_duree[0])+intval($array_duree[1]);//Fait la somme des heures convertit en minutes avec les minutes
            $parcour->setDuree($secondes);
            $parcour->setUser($this->getUser());
            $em->persist($parcour);
            $em->flush();
            return $this->redirectToRoute('menu');
            
        }
        
        return $this->render('parcour_form.html.twig',[
            'form_parcour'=> $form->createView()
        ]);
    }

    //Permet de modifier un parcour
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/modifparcour/{id}", name="modifparcour")
     */
    public function modifparcour(Request $request, $id): Response
    {
        $em= $this->getDoctrine()->getManager();
        $parcour= $this->getDoctrine()->getRepository(Parcour::class)->findOneById($id);
        $form= $this->createForm(ParcourType::class, $parcour);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $parcour->setTypeSortie($form->get('TypeSortie')->getData());
            $parcour->setCommentaire($form->get('commentaire')->getData());
            $parcour->setUser($this->getUser());
            $parcour->setHeureDebut($form->get('heureDebut')->getData());
            $em->persist($parcour);
            $em->flush();
            return $this->redirectToRoute('menu');
        }
        
        return $this->render('parcour_form.html.twig',[
            'form_parcour'=> $form->createView()
        ]);
    }

    //Permet de supprimer un parcour
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/supprparcour/{id}", name="supprparcour")
     */
    public function supprparcour(Request $request, $id): Response
    {
        $em= $this->getDoctrine()->getManager();
        $parcour= $this->getDoctrine()->getRepository(Parcour::class)->findOneById($id);
        $em->remove($parcour);
        $em->flush();
        
        return $this->redirectToRoute('menu');
    }

    /**
     * @Route("/menuJSON", name="menuJSON")
     */
    public function menuJSON(): JsonResponse
    {
        
        if($_GET['type']=="user"){
            $items=$this->getDoctrine()->getRepository(User::class)->findAll();
        }
        elseif($_GET['type']=='serie'){
            $items=$this->getDoctrine()->getRepository(Serie::class)->findAll();
        }
        elseif($_GET['type']=='acteur'){
            $items=$this->getDoctrine()->getRepository(Acteur::class)->findAll();
        }
        else{
            $items=$this->getDoctrine()->getRepository(Personnage::class)->findAll();
        }
        
        
        $data = [];
        foreach($items as $unItem){
            $data[]=$unItem->dataJson();
        }
        
        
        return new JsonResponse($data, Response::HTTP_OK);

       
    }


}
