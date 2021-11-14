<?php
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/Traitements.php');

function getTitreTxt($document){
    //Lire le fichier
    $texte = fopen($document, 'rb');

    //Récupérer les 30 premiers caractères du fichier
    return fgets($texte, 30);
}

function getDescriptionTxt($document){
    //Lire le fichier
    $texte = fopen($document, 'rb');

    //Récupérer les 100 premiers caractères du fichier
    return fgets($texte, 100);
}

function Texte($document)
    {
        //Lire le fichier
        $texte = file_get_contents($document);

        //Transformer le texte en minuscule
        $texte = strtolower($texte);

        // Découpage par taille(on supprime =<2) et par liste de séparateurs
        $tab_mots = tokenisation($texte);

        // Supprimer les mots vides
        $tab_mots = SupprimerMotVides($tab_mots);

        //filtrage des doublons et calcule des occurrences
        return array_count_values($tab_mots);

      //  print_traces_tab($tab_mots_occurences);

    }

