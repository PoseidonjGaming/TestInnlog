<?php
namespace App\Service;

use App\Entity\Parcour;
use App\Entity\User;
use App\Entity\TypeSortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as Write;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;



class Aide extends AbstractController{
    public function import_parcour(){
        $repParcour=$this->getDoctrine()->getRepository(Parcour::class);
       
        $entityManager = $this->getDoctrine()->getManager();
               
        $reader = new Ods(); 
        
        $reader->setReadDataOnly(TRUE);
       
        $spreadsheet = $reader->load($this->getParameter('photo_directory')."import/parcours.ods");
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow(); 
        $maxCol = $worksheet->getHighestColumn(); 
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);
        

        $titreCol=array();

        for($ligne=1; $ligne <= $maxLigne; $ligne++){
           $parcour=new Parcour();
            for($col = 1; $col <= $maxColId; $col++){
                $value = $worksheet->getCellByColumnAndRow($col,$ligne)->getValue();
               
                if($value!=null){
                
                    if($ligne==1){
                        $titreCol[$value]=$col;
                        
                    }
                    else{
                        
                        if($col==$titreCol['Utilisateur']){
                            $user=$this->getDoctrine()->getRepository(User::class)->findUnUserByName($value);
                            if($user==null){
                                $userNew=new User();
                                $encoded = password_hash("P@ssw0rd",PASSWORD_BCRYPT);
                                $userNew->setRoles(["ROLE_admin"]);
                                $userNew->setPassword($encoded);
                                $userNew->setUsername($value);
                                $entityManager->persist($userNew);
                                $parcour->setUser($userNew);
                            }
                            else{
                                $parcour->setUser($user);
                            }
                            
                            
                        }
                        
                        if($col==$titreCol['Type de sortie']){
                            $sortie=$this->getDoctrine()->getRepository(TypeSortie::class)->findUneSortieByName($value);
                            if($sortie==null){
                                $sortieNew=new TypeSortie();
                                
                                $sortieNew->setLibelle($value);
                                $entityManager->persist($sortieNew);
                                $parcour->setTypeSortie($sortieNew);
                            }
                            else{
                                $parcour->setTypeSortie($sortie);
                            }
                            
                            
                        }
                        if($col==$titreCol['Durée']){
                            
                            $parcour->setDuree($value);
                            
                        }
                        if($col==$titreCol['Commentaire']){
                            
                            $parcour->setCommentaire($value);
                            
                        }

                        if($col==$titreCol['Heure de début']){
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                            $parcour->setHeureDebut($date);
                            
                        }
                        
                        if($col==$titreCol['distance']){
                            
                            $parcour->setDistance($value);
                            
                        }

                        if($col==$maxColId){
                            $entityManager->persist($parcour);
                            $entityManager->flush();
                        }
                        
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory')."import/parcours.ods");
        
    }

    public function import_sortie(){
        $repSortie=$this->getDoctrine()->getRepository(TypeSortie::class);
        $entityManager = $this->getDoctrine()->getManager();
               
        $reader = new Ods(); 
        
        $reader->setReadDataOnly(TRUE);
       
        $spreadsheet = $reader->load($this->getParameter('photo_directory')."import/sorties.ods");
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow(); 
        $maxCol = $worksheet->getHighestColumn(); 
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);
        

        $titreCol=array();

        for($ligne=1; $ligne <= $maxLigne; $ligne++){
           $sortie=new TypeSortie();
            for($col = 1; $col <= $maxColId; $col++){
                $value = $worksheet->getCellByColumnAndRow($col,$ligne)->getValue();
               
                if($value!=null){
                
                    if($ligne==1){
                        $titreCol[$value]=$col;
                        
                    }
                    else{
                        
                        if($col==$titreCol['Libellé']){
                            $sortie->setLibelle($value);  
                        }
                        
                        
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory')."import/sorties.ods");
       
    }

   
    public function import_user(){
        $entityManager = $this->getDoctrine()->getManager();
               
    
        
        $reader = new Ods(); 
        
        $reader->setReadDataOnly(TRUE);
       
        $spreadsheet = $reader->load($this->getParameter('photo_directory')."import/users.ods");
        $worksheet = $spreadsheet->getActiveSheet();

        $maxLigne = $worksheet->getHighestRow(); 
        $maxCol = $worksheet->getHighestColumn(); 
        $maxColId = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);
        

        $titreCol=array();

        for($ligne=1; $ligne <= $maxLigne; $ligne++){
           $user=new User();
            for($col = 1; $col <= $maxColId; $col++){
                $value = $worksheet->getCellByColumnAndRow($col,$ligne)->getValue();
               
                if($value!=null){
                
                    if($ligne==1){
                        $titreCol[$value]=$col;
                        
                    }
                    else{
                        
                        if($col==$titreCol['Username']){
                            $user->setUsername($value);
                            
                        }
                        if($col==$titreCol['Role']){
                          $roles=explode(',',$value);
                          $user->setRoles($roles);
                           
                        }
                       
                        

                        if($col==$maxColId){
                            $encoded = password_hash("P@ssw0rd",PASSWORD_BCRYPT);
                            $user->setRoles(["ROLE_USER"]);
                            $user->setPassword($encoded);
                            $entityManager->persist($user);
                            $entityManager->flush();
                            
                        }
                        
                    }
                }
            }
        }
        unlink($this->getParameter('photo_directory')."import/users.ods");
       
    }

    public function export_parcour($get){
        
        $repParcour=$this->getDoctrine()->getRepository(Parcour::class);
        $entityManager = $this->getDoctrine()->getManager();
               
      
        
        
        $spread=new SpreadSheet();
        $writer = new Write($spread); 
        
        $data=[['Utilisateur','Type de sortie','Durée','Commentaire','Heure de début','distance']];
        if($get==[]){
            $parcours=$repParcour->findAll();
        }
        elseif($get!=[]){
            $tab=explode(',',$get['listeExport']);
            $parcours=[];
            foreach($tab as $int){
                $parcour=$entityManager->getRepository(Parcour::class)->findUnParcour($int);
                array_push($parcours,$parcour);
            }
        }
        else{
            $parcours=null;
        }
        
        if($parcours!=null){
            
            foreach($parcours as $unParcour){
                
                $data[]=[$unParcour->getUser()->getUsername(),$unParcour->getTypeSortie()->getLibelle(),$unParcour->getDuree() ,$unParcour->getCommentaire(), \PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel($unParcour->getHeureDebut()),$unParcour->getDistance()];
                
                
                
            }
            $spread->getActiveSheet()->fromArray($data,NULL,'A1');
            $writer->save($this->getParameter('photo_directory').'export/parcours.ods');
            
            
           
        }
        
        
    }

    public function export_sortie($get){
        $repSortie=$this->getDoctrine()->getRepository(TypeSortie::class);
        $entityManager = $this->getDoctrine()->getManager();
        
    
       
        $spread=new SpreadSheet();
        $writer = new Write($spread); 
        
        $data=[['Libellé']];
        
      
        if($get==[]){
            $sorties=$repSortie->findAll();
        }
        elseif($get!=[]){
            $tab=explode(',',$get['listeExport']);
            $sorties=[];
            foreach($tab as $int){
                $sortie=$entityManager->getRepository(TypeSortie::class)->findUneSortie($int);
                array_push($sorties,$sortie);
            }
        }
        else{
            $sorties=null;
        }
       
        if($sorties!=null){
            foreach($sorties as $uneSortie){
            
                $sortie=[$uneSortie->getLibelle()];
                
    
                array_push($data, $sortie);
            }
            $spread->getActiveSheet()->fromArray($data,NULL,'A1');
           
            
            $writer->save($this->getParameter('photo_directory').'export/sorties.ods');     
        }
        
    }
    public function export_user($get){
        $repUser=$this->getDoctrine()->getRepository(User::class);
        $entityManager = $this->getDoctrine()->getManager();
               
       
        $spread=new SpreadSheet();
        $writer = new Write($spread); 
        
        $data=[['Username','Role']];
       
        if($get==[]){
            $users=$repUser->findAll();
        }
        elseif($get!=[]){
            $tab=explode(',',$get['listeExport']);
            $users=[];
            foreach($tab as $int){
                $user=$entityManager->getRepository(User::class)->findUnUser($int);
                array_push($users,$user);
            }
        }
        else{
            $users=null;
        }
        $roles="";
        if($users!=null){
            foreach($users as $unUser){
                foreach($unUser->getRoles() as $unRole){
                    if(array_search($unRole,$unUser->getRoles())<count($unUser->getRoles())){
                        $roles=$roles.$unRole.",";
                    }
                    else{
                        $roles=$roles.$unRole;
                    }
                }
            
                $user=[$unUser->getUsername(),$roles];
                
    
                array_push($data, $user);
            }
            $spread->getActiveSheet()->fromArray($data,NULL,'A1');
            
           
    
            
            $writer->save($this->getParameter('photo_directory').'export/users.ods');    
        }
        
    }
}