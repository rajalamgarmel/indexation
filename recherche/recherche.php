<?php

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/ConnexionBdd.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/Indexation.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/controller/ManagerBdd.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/models/Document.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/models/Mot.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/models/Document_mot.php');

use ManagerBdd\ManagerBdd;
use Document\Document;
use Document_mot\Document_mot;
use Mot\Mot;
    // Récupérer les terms recherchés
    $terms = $_POST['term'];

    //Transformer le texte en minuscule
    $terms = strtolower($terms);

    //Transformer et découper la chaine en ségements
    $tab_mot_recherche = explode(" ", $terms);

    $tab_mot_poids = array();
    for($i=0;$i<count($tab_mot_recherche);$i++){

        $manager_mot = new ManagerBdd();
        $getFromBdd = $manager_mot->mot_isexiste($tab_mot_recherche[$i]);
       // print_r($getFromBdd);
        if ($getFromBdd !== "false"){
            $manager_document_mot = new ManagerBdd();
            $mot_poids = $manager_document_mot->getListe_mot_poids($getFromBdd['id_mot']);
            if($mot_poids == "null"){
                echo "cccccccc";
            }
            else{
                $tab_mot_poids[] = $mot_poids;
            }


        }

    }
    print_r($tab_mot_poids);

    // Compter les occurences de chaque id_document dans le tableau.
    // Occurence correspond à combien de mot recherché trouvé dans un document.

    $tab_idDocument = array();
    foreach ($tab_mot_poids as $key=>$value){
        foreach ($value as $cle=>$valeur){
            foreach ($valeur as $c=>$v) {
                if ($c === 'id_document') {
                    if(array_key_exists($v,$tab_idDocument)){
                        $tab_idDocument[$v]++;
                    }
                    else{
                        $tab_idDocument[$v] = 1;
                    }
                }
            }
        }
    }

    print_r($tab_idDocument);

    // Créer un tableau avec chaque document et la somme des poids des mots trouvés dans ce document.
    $document_sumPoids = array();
    foreach ($tab_mot_poids as $cey=>$calue){
        foreach ($calue as $cey=>$caleur){
            $document_sumPoids[$caleur['id_document']] = 0;
        }
    }
    foreach ($tab_mot_poids as $fey=>$falue){
        foreach ($falue as $fey=>$faleur){
            $document_sumPoids[$faleur['id_document']] += $faleur['poids'];
        }
    }

    print_r($document_sumPoids);

    // combiner les deux tableaux pour avoir un tableau avec le id_document, le nombre des mots recherchés trouvés dans ce document,
    // et la somme des poids de ces mots trouvés
    $tab_DocOccurPoids = array();
    $ligne = 0;
    foreach($tab_idDocument as $cle=>$valeur){
        foreach ($document_sumPoids as $key=>$value){
            if($key===$cle){
                $tab_DocOccurPoids[$ligne]['id_document']=$cle;
                $tab_DocOccurPoids[$ligne]['doc_occurences']=$valeur;
                $tab_DocOccurPoids[$ligne]['poids']=$value;
                $ligne++;
            }
        }
    }
    echo '<br>';
    print_r($tab_DocOccurPoids);

    // Trier le tableau pour que les documents ou tout les mots recherchés ont été trouvés soient affichés en premier
    // En suite si on a plusieur documents ou les tout les mots sont trouvés on va faire un autre tri pour que les documents
    //qui ont la somme des poids la plus grande soient affichés en premier.

    array_multisort (array_column($tab_DocOccurPoids, 'doc_occurences'), SORT_DESC, $tab_DocOccurPoids,
        array_column($tab_DocOccurPoids, 'poids'), SORT_DESC, $tab_DocOccurPoids);

    echo '<br>';
    print_r($tab_DocOccurPoids);
    //print_r($tab_idDocument);
    //print_r($document_sumPoids);


