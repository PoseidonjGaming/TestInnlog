<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TypeSortie;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\SortieFormType;

class TypeSortieController extends AbstractController
{
    /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/sortie", name="gerer_sortie")
     */
    public function gerer_parcour(Request $request): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $rep=$this->getDoctrine()->getRepository(TypeSortie::class);  
        $sorties=$rep->findAll();

        $sortie=new TypeSortie();
        if(isset($_POST['ID'])){
            $searchSortie=$rep->findUneSortie($_POST['ID']);
            if($searchSortie!=null){
                $sortie=$searchSortie;
            }
        }
        
        $form = $this->createForm(SortieFormType::class,$sortie);
        $form->handleRequest($request);
        $error=' ';

        if ($form->isSubmitted() && $form->isValid()) {

                
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($sortie);
                $manager->flush();
            
            return $this->redirectToRoute('gerer_sortie');
           
        }

        return $this->render('type_sortie/gerer_sortie.html.twig', [
            'sorties' => $sorties,
            'formSortie'=>$form->createView(),
            'error'=>$error
          
        ]);
    }
   

    //Permet de supprimer un parcour
    /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/supprimer_sortie/{id}", name="supprimer_sortie")
     */
    public function supprimer_parcour(Request $request, $id): Response
    {
        $em= $this->getDoctrine()->getManager();
        $parcour= $this->getDoctrine()->getRepository(TypeSortie::class)->findUneSortie($id);
        $em->remove($parcour);
        $em->flush();
        
        return $this->redirectToRoute('gerer_sortie');
    }

    /**
     * @IsGranted("ROLE_super_admin")
     * @Route("/supprimer_sorties", name="supprimer_sorties")
     */
    public function supprimer_parcours(): Response
    {
        $tab=array_keys($_GET);
        
        $entityManager=$this->getDoctrine()->getManager();
        
        $exclude=["sortie","checkExport","checkall"];
        foreach($tab as $int){
            if(!in_array($int,$exclude)){
                $parcour=$entityManager->getRepository(TypeSortie::class)->findUneSortie($int);

                $entityManager->remove($parcour);
                $entityManager->flush();
            }

            
        }
        return $this->redirectToRoute('gerer_sortie');
    }

}
