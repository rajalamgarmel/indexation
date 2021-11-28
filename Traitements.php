<?php

//Fonction de découpage de chaine de caratères en ségements
function tokenisation ( $texte)
{
    //définir la liste des spérateurs
    $separateurs = " ',’.():!?»«\'\t\"\n\r'- +/*%{}[]#";

    $tabtok = array();

    $tok = strtok($texte, $separateurs);

    while ($tok !== false)
    {
        if( strlen($tok) > 2 )$tabtok[] = $tok;
        $tok = strtok($separateurs);
    }
    return $tabtok;
}

//Fonction de filtrage et de suppression des mots vides.
function SupprimerMotVides($tab)
{
    //Lire le fichier qui contient les mots vides
    $fichier_motsvides = file(realpath($_SERVER["DOCUMENT_ROOT"]) . '\indexation\stop_words_french.txt');
    $tab_motsvides = array();
    $tab_mots = array();
    $j = 0;

    //transformer le fichier en tableau
    for($i=0;$i<count($fichier_motsvides);$i++){
        $tab_motsvides[$i] = trim(strtolower($fichier_motsvides[$i]));
    }

    // on récupére les mots qui ne sont pas dans la liste des mots vides.
    foreach ($tab as $ligne) {
        if (!in_array(strtolower(trim($ligne)), $tab_motsvides)) {
            $tab_mots[$j] = $ligne;
            $j++;
        }
    }

    return $tab_mots;
}

function print_traces_tab($tab_tok)
{
    foreach ($tab_tok  as $indice => $valeur)
    {
        echo "$indice : $valeur", '<br>';
    }
}

//Fonction pour générer le cloud à partir des données fournies
function genererNuage( $data = array() , $minFontSize = 10, $maxFontSize = 36 )
{
    $tab_colors=array("#3087F8", "#7F814E", "#EC1E85","#14E414","#9EA0AB", "#9EA414");

    $minimumCount = min( array_values( $data ) );
    $maximumCount = max( array_values( $data ) );
    $spread = $maximumCount - $minimumCount;

    $cloudTags = array();

    $spread == 0 && $spread = 1;
    //Mélanger un tableau de manière aléatoire
    srand((float)microtime()*1000000);
    $mots = array_keys($data);
    shuffle($mots);

    foreach( $mots as $tag )
    {
        $count = $data[$tag];

        //La couleur aléatoire
        $color=rand(0,count($tab_colors)-1);

        $size = $minFontSize + ( $count - $minimumCount )
            * ( $maxFontSize - $minFontSize ) / $spread;
        $cloudTags[] ='<span style="font-size: '. floor( $size ) . 'px' . ';
         color:' . $tab_colors[$color]. '; ">' .
            $tag .
            '</span>';
    }
    return join( "\n", $cloudTags ) . "\n";
}

?>

