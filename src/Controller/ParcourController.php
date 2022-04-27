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
  
    /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/parcour", name="gerer_parcour")
     */
    public function gerer_parcour(Request $request): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $rep=$this->getDoctrine()->getRepository(Parcour::class);  
        $parcours=$rep->findAll();

        $user=new Parcour();
        if(isset($_POST['ID'])){
            $searchParcour=$rep->findUnParcour($_POST['ID']);
            if($searchParcour!=null){
                $user=$searchParcour;
            }
        }
        
        $form = $this->createForm(ParcourType::class,$user);
        $form->handleRequest($request);
        $error=' ';

        if ($form->isSubmitted() && $form->isValid()) {

                
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
            
            return $this->redirectToRoute('gerer_parcour');
           
        }

        return $this->render('parcour/gerer_parcour.html.twig', [
            'parcours' => $parcours,
            'formUser'=>$form->createView(),
            'error'=>$error
          
        ]);
    }
   

    //Permet de supprimer un parcour
    /**
     * @IsGranted("ROLE_super_admin")
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

   
}
