buttonsRow=""
sortie=""

function verif(e){
    bool=true
    
    if(window.sortie!=""){
        bool=bool && e['libelle'].includes(window.sortie)
    }

    return bool
    
    
}


function filtre(min,max,mod){
    
    
    deleteRow()    
    window.ListeFitre.forEach(function(e){
        
        row=document.createElement('tr')
       
            
            
        addCol(e,row,window.ListeFitre,min,max)
        colButton=window.buttonsRow.cloneNode(true)
       
        
        
      
        colButton.children[0].setAttribute('onclick','modifier("'+e['libelle']+'","'+e['id']+'")')
        colButton.children[0].setAttribute('id','modif_"'+e['id'])
        colButton.children[0].setAttribute('name','modif_"'+e['id'])
        colButton.children[1].setAttribute('onclick','supprimer("'+e['id']+'")')
        colButton.children[1].setAttribute('id','sup_"'+e['id'])
        colButton.children[1].setAttribute('name','sup_"'+e['id'])
        

        row.appendChild(colButton)
        if(window.boolExport){
            th=document.createElement('td')
            th.style="text-align: center; vertical-align: middle;"
            check=document.createElement('input')
            check.type='checkbox'
            check.setAttribute('id',e['id'])
            check.setAttribute('name',e['id'])
            check.setAttribute('onclick','addExport("'+e['id']+'")')
            console.log(window.listeExport,e['id'].toString())

            if(window.listeExport.includes(e['id'].toString())){
                check.checked=true
            }
            th.appendChild(check)
            row.appendChild(th)
        }
        else{
            row.children[1].setAttribute('colspan','1')
        }
        
        if(row.children['length']>0){
            window.tbody.appendChild(row)
        }
        
    })
    if(window.boolExport==false){
        pager(window.ListeFitre)
    }
    
    
        
        
}

function modifier(nom,id){
        
    if(id!=null){
        document.getElementById('sortie_form_libelle').setAttribute('value', nom);
        document.getElementById('ID').setAttribute('value',id);
        document.getElementById('titreModaleSortie').innerHTML="Modification de la sortie"            
    }
    else{
        document.getElementById('sortie_form_libelle').setAttribute('value','');
        document.getElementById('ID').setAttribute('value','');
        document.getElementById('titreModaleSortie').innerHTML="Ajouter une sortie"        
    }
    
}


function supprimer(Id){
    document.getElementById('submitAutre').setAttribute('class','btn btn-danger');
    
    document.getElementById('supModalLongTitle').innerHTML="Suppression"
    if(Id==null){
        document.getElementById('pModalAutre').innerHTML="Supprimer ces sorties";
        document.getElementById('titreModaleSortie').innerHTML="Supprimer les sorties"
        document.getElementById('form').action='/supprimer_sorties';
    }
    else{
        document.getElementById('pModalAutre').innerHTML="Supprimer cette sorties";
        document.getElementById('titreModaleSortie').innerHTML="Supprimer une sortie"
        document.getElementById('form').action='/supprimer_sortie/'+Id;
    }
}

function exporter(){
    
    document.getElementById('submitAutre').setAttribute('class','btn btn-primary');
    document.getElementById('pModalAutre').innerHTML="Exporter ces séries";
    document.getElementById('supModalLongTitle').innerHTML="Exportation"
   
    document.getElementById('form').action='/export'

    input=document.createElement('input')
    input.type="hidden"
    input.setAttribute('id','listeExport')
    input.setAttribute('name','listeExport')
    Listvalue=window.listeExport[0]
    window.listeExport.splice(0,1)
    window.listeExport.forEach(function(e){
        Listvalue=Listvalue+","+e
    })
    input.value=Listvalue
    
    document.getElementById('pModalAutre').appendChild(input)
    
    
}

function modExport(){
    
    if(document.getElementById("checkExport").checked){
        window.boolExport=true
        document.getElementById("checkall").disabled = false
        
    }
    else{
        window.boolExport=false
        document.getElementById("checkall").disabled = true

    }
    modif(0,10,1,window.ListeBase)
}
function addExport(id){
    console.log(document.getElementById(id))
    if(document.getElementById(id).checked){
        window.listeExport.push(id)
    }
    else{
        index=window.listeExport.indexOf(id)
        window.listeExport.splice(index,1)
    }
}




