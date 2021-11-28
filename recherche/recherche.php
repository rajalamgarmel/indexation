<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        #gauche {
            float:left;

        }
        #center {
            float:left;
            margin-left: 2%;
            margin-right: 2%;
        }
    </style>
    <title>Recheche Paris 8</title>
</head>
<body>

<div class="d-flex justify-content-center"><img src="../logo.PNG"></div>

<div class="container">
    <div class="row justify-content-center">

        <div class="col-8">
            <form class="d-flex justify-content-center" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" >
                <input class="form-control" type="search" name="term" placeholder="<?php echo htmlspecialchars($_POST['term']); ?>" aria-label="Search">
                <button class="btn btn-primary" type="submit">Rechercher</button>
            </form>
        </div>

    </div>
</div>



<div id="wrapper">
    <div id="header" class="container-fluid mb-0 p-0">
    </div>
    <div id="content" class="container d-flex flex-column">

        <?php

        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/ConnexionBdd.php');
        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/IndexationTEXTE.php');
        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/Traitements.php');
        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/ManagerBdd.php');
        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/Document.php');
        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/Mot.php');
        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/basededonnees/Document_mot.php');
        require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/indexation/nuage_des_mots.php');
        use ManagerBdd\ManagerBdd;

        // Récupérer les terms recherchés
        $terms = $_POST['term'];

        //Transformer le texte en minuscule
        $terms = strtolower($terms);

        //Transformer et découper la chaine en ségements
        $tab_mot_recherche = explode(" ", $terms);

        $tab_mot_poids = array();
        for($i=0;$i<count($tab_mot_recherche);$i++){

            $sql = new ManagerBdd();
            $resultat = $sql->mot_isexiste($tab_mot_recherche[$i]);
            // print_r($getFromBdd);
            if ($resultat !== "false"){
                $sql = new ManagerBdd();
                $mot_poids = $sql->getListe_mot_poids($resultat['id_mot']);
                if($mot_poids == "null"){
                    echo "cccccccc";
                }
                else{
                    $tab_mot_poids[] = $mot_poids;
                }
            }

        }
        //print_r($tab_mot_poids);

        // Compter les occurences de chaque id_document dans le tableau.
        // Occurence correspond à combien de mot recherché trouvé dans un document.

        $tab_combien_mot_trouve = array();
        foreach ($tab_mot_poids as $key=>$value){
            foreach ($value as $cle=>$valeur){
                foreach ($valeur as $c=>$v) {
                    if ($c === 'id_document') {
                        if(array_key_exists($v,$tab_combien_mot_trouve)){
                            $tab_combien_mot_trouve[$v]++;
                        }
                        else{
                            $tab_combien_mot_trouve[$v] = 1;
                        }
                    }
                }
            }
        }

        //print_r($tab_combien_mot_trouve);

        // Créer un tableau avec chaque document et la somme des poids des mots trouvés dans ce document.
        $document_SommePoids = array();
        foreach ($tab_mot_poids as $key=>$value){
            foreach ($value as $cle=>$valeur){
                $document_SommePoids[$valeur['id_document']] = 0;
            }
        }
        foreach ($tab_mot_poids as $c=>$v){
            foreach ($v as $kc=>$val){
                $document_SommePoids[$val['id_document']] += $val['poids'];
            }
        }

        //print_r($document_sumPoids);

        // combiner les deux tableaux pour avoir un tableau avec le id_document, le nombre des mots recherchés trouvés dans ce document,
        // et la somme des poids de ces mots trouvés
        $tab_document_motTrouve_poids = array();
        $ligne = 0;
        foreach($tab_combien_mot_trouve as $cle=>$valeur){
            foreach ($tab_combien_mot_trouve as $key=>$value){
                if($key===$cle){
                    $tab_document_motTrouve_poids[$ligne]['id_document']=$cle;
                    $tab_document_motTrouve_poids[$ligne]['doc_occurences']=$valeur;
                    $tab_document_motTrouve_poids[$ligne]['poids']=$value;
                    $ligne++;
                }
            }
        }
        echo '<br>';
        //print_r($tab_document_motTrouve_poids);

        // Trier le tableau pour que les documents ou tout les mots recherchés ont été trouvés soient affichés en premier
        // En suite si on a plusieur documents ou tout les mots sont trouvés on va faire un autre tri pour que les documents
        //qui ont la somme des poids la plus grande soient affichés en premier.

        array_multisort (array_column($tab_document_motTrouve_poids, 'doc_occurences'), SORT_DESC, $tab_document_motTrouve_poids,
            array_column($tab_document_motTrouve_poids, 'poids'), SORT_DESC, $tab_document_motTrouve_poids);


        // Affichage des résultats
        foreach ($tab_document_motTrouve_poids as $key=>$value){
            // echo $tab_DocOccurPoids[$key]['id_document'];
            $sql = new ManagerBdd();
            $resultat = $sql->resultat_a_affiche($tab_document_motTrouve_poids[$key]['id_document']);
            if (count($resultat) > 0) {
                $lien = '../documents/';
                ?>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8">
                            <div id="gauche"><a href="<?php echo $lien . $resultat['nom_document'];?>">
                                    <h6> <?php echo $resultat['titre_document']; ?></h6>
                                </a>
                            </div>
                            <div id="center"><span class="badge bg-primary"><?php echo $tab_document_motTrouve_poids[$key]['poids']; ?></span></div>
                            <a class="btn btn-primary btn-sm" data-bs-toggle="collapse"
                               href="#ids<?php echo $resultat['id_document']; ?>"
                               role="button" aria-expanded="false" aria-controls="collapseExample">
                                +
                            </a>
                            <p><?php echo $resultat['description']; ?>...</p>
                        </div>

                        <div class="col-sm-4">
                            <div class="collapse" id="ids<?php echo $resultat['id_document']; ?>">
                                <div class="card card-body">
                                    <?php $nuagemots = $sql->Liste_mot_poids($resultat['id_document']);
                                    foreach ($nuagemots as $key=>$value){
                                        $tab_nuagemots[$nuagemots[$key]['mot']] = $nuagemots[$key]['poids'];
                                    }
                                    ?>
                                    <div id="tagcloud">
                                        <?php echo genererNuage( $tab_nuagemots ) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } else { echo "walo";
            }

        }

        echo '<br>';
        //print_r($tab_DocOccurPoids);
        //print_r($tab_idDocument);
        //print_r($document_sumPoids);
        ?>
    </div>
</div>



<div id="footer" class="container-fluid p-0 mt-auto"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
