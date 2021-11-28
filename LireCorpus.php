<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
    </style>
    <title>Recheche Paris 8</title>
</head>
<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/ConnexionBdd.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/IndexationTEXTE.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/ManagerBdd.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/Document.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/Mot.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/Document_mot.php');


use ManagerBdd\ManagerBdd;
use Document\Document;
use Document_mot\Document_mot;
use Mot\Mot;

/** Afficher les erreurs */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/** Upload le fichier */

$dossier = realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/documents/';
$nb_fichier = 0;

if($mondossier = opendir('./documents'))
{
   // closedir($mondossier);
    while(false !== ($fichier = readdir($mondossier))) {
        if ($fichier != '.' && $fichier != '..') {
            $nb_fichier++; // On incrémente le compteur de 1

            //verifier si le fichier existe dans la base de données
            $sql = new ManagerBdd(); // on instancie l'objet ManagerBdd ou se trouve la logique de la base de données et les requetes.
            $fichier_existe = $sql->fichier_isexiste($fichier);// on fait appel à la requetes

            if ($fichier_existe) { // si le fichier existe déjà dans la base de données
                echo $fichier;
                $sql = new ManagerBdd();// on instancie l'objet ManagerBdd ou se trouve la logique de la base de données et les requetes.

                $idDocument_asupprimer = $sql->getid_document($fichier);//on récupére l'id du document existant.
                $listeMots_asupprimer = $sql->getliste_mots($idDocument_asupprimer['id_document']);//on récupére la liste des mots de ce document.

                $sql->deletfrom_document($idDocument_asupprimer['id_document']);//on supprime le document de la base de données
                $sql->deletfrom_document_mot($idDocument_asupprimer['id_document']);//on supprime le document, les mots et les poids de la base de données

                foreach ($listeMots_asupprimer as $mot_a_supprimer) {
                    $sql->deletfrom_mot($mot_a_supprimer['id_mot']);//on supprime les mots de la table mot.
                }
            }

            // sinon dans tout les cas on va appeler la methode indexationTexte et le traitement base de données
            /** Indexation  */
            $tab_mots = Texte($dossier.$fichier); // Methode d'indexation des fichier .txt on aura un tableau de mots
            $titre = getTitreTxt($dossier.$fichier); // Récupérer le titre d'un fichier texte
            $description = getDescriptionTxt($dossier.$fichier); // Récupérer la description d'un fichier texte

            /** Traitement Base de données */
            $document = new Document(); // on instancie l'objet Document qui correspond à notre table document
            $document->setNom_document($fichier);//dans la table document on affecte le nom du fichier
            $document->setTitre_document($titre);//dans la table document on affecte le titre du fichier qu'on a récuperé avec getTitreTxt
            $document->setDescription($description);//dans la table document on affecte le titre du fichier qu'on a récuperé avec getDescriptionTxt

            $sql = new ManagerBdd(); // on instancie l'objet ManagerBdd ou se trouve la logique de la base de données et les requetes.
            $addToBdd = $sql->add_document($document); // on appele la fonction qui va faire l'insertion dans la table document.

            $idDocument = $document->getId_document(); // on récupére l'id document pour pouvoir l'ajouter dans la table doc_mot

            $mots = new Mot(); // on instancie l'objet Document qui correspond à notre table mot
            $document_mot = new Document_mot(); // on instancie l'objet Document qui correspond à notre table document_mot

            foreach ($tab_mots as $mot => $poids) {

                if (!is_null($sql->mot_isexiste($mot))) {// on vérifier l'existance de mot dans la table mot
                    $idMot = $sql->mot_isexiste($mot)['id_mot']; // s'il existe on récupére son id, sans l'ajouter dans la table mot
                } else {
                    $mots->setMot($mot);
                    $addToBdd = $sql->add_mot($mots);// s'il n'existe pas on l'ajoute dans la table mot

                    $idMot = $mots->getId_mot(); // puis on récupére son Id
                }

                $document_mot->setId_document($idDocument); // dans la table document_mot on affecte l'id doc qu'on a récupéré
                $document_mot->setId_mot($idMot); // dans la table document_mot on affecte l'id mot qu'on a récupéré
                $document_mot->setPoids($poids); // dans la table document_mot on affecte le poids  qu'on a calculé

                $addToBdd = $sql->add_document_mot($document_mot);// on appele la fonction qui va faire l'insertion dans la table doc_mot
            }

        } // On termine la boucle

    }
    closedir($mondossier);
}

else{
    echo 'Le dossier n\' a pas pu être ouvert';
}
?>
</html>
