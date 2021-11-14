<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> Notes palier MEDOC </title>
</head>
<body>
</body>
</html>
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

/** Afficher les erreurs */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/** Upload le fichier */

$dossier = realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/upload/';
$fichier = basename($_FILES['avatar']['name']);

if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
{
    /** Vérifier si le fichier existe déja */

    if(!file_exists($dossier . $fichier)) {
        //si le fichier n'existe pas, on l'upload.
        move_uploaded_file($_FILES['avatar']['tmp_name'], $dossier . $fichier);
    }else{
        //si le fichier existe déja.
        $manager = new ManagerBdd();// on instancie l'objet ManagerBdd ou se trouve la logique de la base de données et les requetes.

        $idDocument_asupprimer = $manager->getid_document($fichier);//on récupére l'id du document existant.
        $listeMots_asupprimer = $manager->getliste_mots($idDocument_asupprimer['id_document']);//on récupére la liste des mots de ce document.

        $manager->deletfrom_document($idDocument_asupprimer['id_document']);//on supprime le document de la base de données
        $manager->deletfrom_document_mot($idDocument_asupprimer['id_document']);//on supprime le document, les mots et les poids de la base de données

        foreach ($listeMots_asupprimer as $mot_a_supprimer){
            $manager->deletfrom_mot($mot_a_supprimer['id_mot']);//on supprime les mots de la table mot.
        }
        //on écrase l'ancien fichier et on réupload le nouveau fichier.
        move_uploaded_file($_FILES['avatar']['tmp_name'], $dossier . $fichier);
    }

}
else
{
    echo $erreur;
}

/** Indexation  */
$tab_mots = Texte($dossier . $fichier); // Methode d'indexation des fichier .txt
$titre = getTitreTxt($dossier . $fichier); // Récupérer le titre d'un fichier texte
$description = getDescriptionTxt($dossier . $fichier); // Récupérer la description d'un fichier texte


$document = new Document(); // on instancie l'objet Document qui correspond à notre table document
$document->setNom_document($fichier);//dans la table document on affecte le nom du fichier
$document->setTitre_document($titre);//dans la table document on affecte le titre du fichier qu'on a récuperé avec getTitreTxt
$document->setDescription($description);//dans la table document on affecte le titre du fichier qu'on a récuperé avec getDescriptionTxt

$manager = new ManagerBdd(); // on instancie l'objet ManagerBdd ou se trouve la logique de la base de données et les requetes.
$addToBdd = $manager->add_document($document); // on appele la fonction qui va faire l'insertion dans la table document.

$idDocument = $document->getId_document(); // on récupére l'id document pour pouvoir l'ajouter dans la table doc_mot

$mots = new Mot(); // on instancie l'objet Document qui correspond à notre table mot
$document_mot = new Document_mot(); // on instancie l'objet Document qui correspond à notre table document_mot

foreach($tab_mots as $mot => $poids){

    if (!is_null($manager->mot_isexiste($mot))){// on vérifier l'existance de mot dans la table mot
        $idMot = $manager->mot_isexiste($mot)['id_mot']; // s'il existe on récupére son id, sans l'ajouter dans la table mot
    }
    else{
        $mots->setMot($mot);
        $addToBdd = $manager->add_mot($mots);// s'il n'existe pas on l'ajoute dans la table mot

        $idMot = $mots->getId_mot(); // puis on récupére son Id
    }

    $document_mot->setId_document($idDocument); // dans la table document_mot on affecte l'id doc qu'on a récupéré
    $document_mot->setId_mot($idMot); // dans la table document_mot on affecte l'id mot qu'on a récupéré
    $document_mot->setPoids($poids); // dans la table document_mot on affecte le poids  qu'on a calculé

    $addToBdd = $manager->add_document_mot($document_mot);// on appele la fonction qui va faire l'insertion dans la table doc_mot
}


?>