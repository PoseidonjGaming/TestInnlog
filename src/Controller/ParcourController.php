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

class ParcourController extends AbstractController
{
    //Route d'accueil après la connexion
    /**
     * @Route("/menu", name="menu")
     */
    public function menu(): Response
    {
        $parcours=$this->getDoctrine()->getRepository(Parcour::class)->findAll();
        return $this->render('menu.html.twig', [
            'parcours' => $parcours,
            
          
        ]);
    }


    //Route qui permet d'ajouter et modifier les parcours
    /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/parcour", name="gerer_parcour")
     */
    public function gerer_parcour(Request $request): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $rep=$this->getDoctrine()->getRepository(Parcour::class);  
        $parcours=$rep->findAll();

        $parcour=new Parcour();
        if(isset($_POST['ID'])){
            $searchParcour=$rep->findUnParcour($_POST['ID']);
            if($searchParcour!=null){
                $parcour=$searchParcour;
            }
        }
        
        $form = $this->createForm(ParcourType::class,$parcour);
        $form->handleRequest($request);
        $error=' ';

        if ($form->isSubmitted() && $form->isValid()) {

                
                $parcour->setDistance($_POST['distance']);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($parcour);
                $manager->flush();
            
            return $this->redirectToRoute('gerer_parcour');
           
        }

        return $this->render('parcour/gerer_parcour.html.twig', [
            'parcours' => $parcours,
            'formParcour'=>$form->createView(),
            'error'=>$error
          
        ]);
    }
   

    //Permet de supprimer un parcours
    /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/supprimer_parcour/{id}", name="supprimer_parcour")
     */
    public function supprimer_parcour(Request $request, $id): Response
    {
        $em= $this->getDoctrine()->getManager();
        $parcour= $this->getDoctrine()->getRepository(Parcour::class)->findUnParcour($id);
        $em->remove($parcour);
        $em->flush();
        
        return $this->redirectToRoute('gerer_parcour');
    }


    //Route qui permet de supprimer plusieurs ou tous les parcours
    /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/supprimer_parcours", name="supprimer_parcours")
     */
    public function supprimer_parcours(): Response
    {
        $tab=array_keys($_GET);
        $exclude=["user","dateD","heureD","distance","checkExport","checkall","sortie"];
        $entityManager=$this->getDoctrine()->getManager();
       
        foreach($tab as $int){
            
            if(!in_array($int,$exclude)){
                $parcour=$entityManager->getRepository(Parcour::class)->findUnParcour($int);

                $entityManager->remove($parcour);
                $entityManager->flush();
            }

            
        }
        return $this->redirectToRoute('gerer_parcour');
        
    }

   
}
