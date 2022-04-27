<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Parcour;
use App\Entity\TypeSortie;
use App\Form\ParcourType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Aide;

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
        
        if($_GET['type']=="user"){
            $items=$this->getDoctrine()->getRepository(User::class)->findAll();
        }
        elseif($_GET['type']=='parcour'){
            $items=$this->getDoctrine()->getRepository(Parcour::class)->findAll();
        }
        else{
            $items=$this->getDoctrine()->getRepository(TypeSortie::class)->findAll();
        }
        
        
        $data = [];
        foreach($items as $unItem){
            $data[]=$unItem->dataJson();
        }
        
       
        return new JsonResponse($data, Response::HTTP_OK);

       
    }

   

     /**
     * @Route("/test", name="test")
     */
    public function test(Request $request): Response
    { 
        
      
        dump($_GET);
        return $this->render('test.html.twig', [
            
        ]);
        
        
    }
    /**
     * @IsGranted("ROLE_admin")
     * @Route("/export", name="export")
     */
    public function export(Aide $aide): Response
    {
        
        if(isset($_POST['parcours']) || (isset($_GET['type'])&&$_GET['type']=='parcour')){
           
            $aide->export_parcour($_GET);
            
           
        }
        if(isset($_POST['sortiesExport'])|| (isset($_GET['type'])&&$_GET['type']=='sortie')){
           $aide->export_sortie($_GET);
        }
        if(isset($_POST['usersExport']) || (isset($_GET['type'])&&$_GET['type']=='user')){
           $aide->export_user($_GET);
        }
        
        
        
        $zip=new \ZipArchive();
        $zip->open($this->getParameter('photo_directory').'/export//'.'Export_donnée.zip',\ZipArchive::CREATE);
        
        
        
        $zip->addFile($this->getParameter('photo_directory').'/export//'.'parcours.ods',"parcours.ods");
        $zip->addFile($this->getParameter('photo_directory').'/export//'.'sorties.ods','sorties.ods');
        $zip->addFile($this->getParameter('photo_directory').'/export//'.'users.ods','users.ods');
        

        
        
        $zip->close();
        
        if(file_exists($this->getParameter('photo_directory').'/export//'."Export_donnée.zip")){
            $response = new Response(file_get_contents($this->getParameter('photo_directory')."export/".'Export_donnée.zip'));
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition: attachment; filename=', 'Export_donnée.zip');
            
            unlink($this->getParameter('photo_directory').'/export//'.'Export_donnée.zip');
            if(file_exists($this->getParameter('photo_directory')."export/parcours.ods")){
                unlink($this->getParameter('photo_directory')."export/parcours.ods");
            }
            if(file_exists($this->getParameter('photo_directory')."export/sorties.ods")){
                unlink($this->getParameter('photo_directory')."export/sorties.ods");
            }
            if(file_exists($this->getParameter('photo_directory')."export/users.ods")){
                unlink($this->getParameter('photo_directory')."export/users.ods");
            }
            
            return $response;
            
           
        }
        else{
            //return $this->redirectToRoute('menu');
            dump($_GET);
            
            return $this->render('test.html.twig', [
                
            
            ]);
            
        }
        
    }

    /**
     * @IsGranted("ROLE_admin")
     * @Route("/import", name="import")
     */
    public function import(Aide $aide): Response
    {
        
        if($_FILES['fichierParcour']['tmp_name']!=""){
            copy($_FILES['fichierParcour']['tmp_name'],$this->getParameter('photo_directory').'/import//'.'parcours.ods');
           
            $aide->import_parcour();
           
        }
        if($_FILES['fichierSortie']['tmp_name']!=''){
           
            copy($_FILES['fichierSortie']['tmp_name'],$this->getParameter('photo_directory').'/import//'.'sorties.ods');
            $aide->import_sortie();
        } 
        if($_FILES['fichierUser']['tmp_name']!=""){
        
            copy($_FILES['fichierUser']['tmp_name'],$this->getParameter('photo_directory').'/import//'.'users.ods');
            $aide->import_user();
            
            
        }
        
        
        
        /*return $this->render('home/test.html.twig', [
            
          
        ]);*/
        
        return $this->redirectToRoute('menu');
        
    }


}
