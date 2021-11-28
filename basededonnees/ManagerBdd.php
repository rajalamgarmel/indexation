<?php
namespace ManagerBdd;
use PDO;

use Document\Document;
use Mot\Mot;
use Document_mot\Document_mot;

class ManagerBdd extends ConnexionBdd
{

    // vérifier si le fichier existe déjà dans la base de données
    public function fichier_isexiste($fichier)
    {

        $q = $this->pdo->prepare("SELECT * FROM document   
                                            WHERE nom_document=:nom_document");

        $q ->bindValue(':nom_document', $fichier, PDO::PARAM_STR);

        $excutIsOk = $q->execute();

        if(!$excutIsOk){
            print_r($q->errorInfo());
            return false;
        }
        else {
            return true;
        }
    }

    // vérifier si le mot existe dans la base de données
    public function mot_isexiste($term)
    {
        $this->pdoStatement= $this->pdo->prepare('SELECT * from mot WHERE mot=:mot');
        $this->pdoStatement->bindValue(':mot', $term, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            $mot = $this->pdoStatement->fetch(PDO::FETCH_ASSOC);
            if($mot === false){
                return null;
            }
            else{
                return $mot;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    // ajouter le document dans la base de données
    public function add_document(Document &$document)
    {

        $q = $this->pdo->prepare("INSERT INTO document ( nom_document, titre_document, description)  
                                            VALUES(  :nom_document, :titre_document, :description)");


        $q ->bindValue(':nom_document', $document->getNom_document(), PDO::PARAM_STR);
        $q ->bindValue(':titre_document', $document->getTitre_document(), PDO::PARAM_STR);
        $q ->bindValue(':description', $document->getDescription(), PDO::PARAM_STR);

        $excutIsOk = $q->execute();

        if(!$excutIsOk){
            print_r($q->errorInfo());
            return false;
        }
        else {
            $id = $this->pdo->lastInsertId();
            $document = $this->read_id_document($id);
            return true;
        }
    }

    // ajouter un mot dans la base de données
    public function add_mot(Mot &$mot)
    {
        $q = $this->pdo->prepare("INSERT INTO mot ( mot)  
                                            VALUES( :mot)");

        $q ->bindValue(':mot', $mot->getMot(), PDO::PARAM_STR);

        $excutIsOk = $q->execute();

        if(!$excutIsOk){
            print_r($q->errorInfo());
            return false;
        }
        else {
            $id = $this->pdo->lastInsertId();
            $mot = $this->read_id_mot($id);
            return true;
        }
    }

    // ajouter les documents et les mots dans la table document mot
    public function add_document_mot(Document_mot &$document_mot)
    {
        $q = $this->pdo->prepare("INSERT INTO document_mot (id_document, id_mot, poids)  
                                            VALUES( :id_document, :id_mot, :poids)
                                            ");

        $q ->bindValue(':id_document', $document_mot->getId_document(), PDO::PARAM_STR);
        $q ->bindValue(':id_mot', $document_mot->getId_mot(), PDO::PARAM_STR);
        $q ->bindValue(':poids', $document_mot->getPoids(), PDO::PARAM_STR);

        $excutIsOk = $q->execute();

        if(!$excutIsOk){
            print_r($q->errorInfo());
            return false;
        }
        else {
            //$id = $this->pdo->lastInsertId();
            //$document_mot = $this->read_id_mot($id);
            return true;
        }
    }

    // récupérer l'id document du dérnier enregistrement
    public function read_id_document($id)
    {
        $this->pdoStatement= $this->pdo->prepare('SELECT * from document WHERE id_document=:id');
        $this->pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            $document = $this->pdoStatement->fetchObject('Document\Document');
            if($document === false){
                return null;
            }
            else{
                return $document;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    // Récupérer l'id document (à partir de nom du document)
    public function getid_document($fichier)
    {
        $this->pdoStatement= $this->pdo->prepare('SELECT * from document WHERE nom_document=:nom');
        $this->pdoStatement->bindValue(':nom', $fichier, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            $document = $this->pdoStatement->fetch(PDO::FETCH_ASSOC);
            if($document === false){
                return null;
            }
            else{
                return $document;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }


    // récupérer l'id mot
    public function read_id_mot($id)
    {
        $this->pdoStatement= $this->pdo->prepare('SELECT * from mot WHERE id_mot=:id');
        $this->pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            $mot = $this->pdoStatement->fetchObject('Mot\Mot');
            if($mot === false){
                return null;
            }
            else{
                return $mot;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    // récupérer la liste des mots et leurs poids (a partir d'un id mot)
    public function getListe_mot_poids($mot)
    {   $i=0;
        $mots_avecPoids = array();
        $this->pdoStatement = $this->pdo->prepare('SELECT * from document_mot dm
                                                        INNER JOIN mot m
                                                        ON dm.id_mot = m.id_mot
                                                        WHERE dm.id_mot = :id_mot');

        $this->pdoStatement->bindValue(':id_mot', $mot, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            $mots = $this->pdoStatement->fetchAll(PDO::FETCH_ASSOC);

            if($mots === false){
                print_r($this->pdoStatement->errorInfo());
                return null;
            }
            else{
                return $mots;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    // récupérer la liste des mots et leurs poids (a partir d'un id document)
    public function Liste_mot_poids($id_document)
    {
        $this->pdoStatement = $this->pdo->prepare('SELECT m.mot,dm.poids from document_mot dm
                                                        INNER JOIN mot m
                                                        ON dm.id_mot = m.id_mot
                                                        WHERE dm.id_document = :id_document');

        $this->pdoStatement->bindValue(':id_document', $id_document, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            $mots = $this->pdoStatement->fetchAll(PDO::FETCH_ASSOC);

            if($mots === false){
                print_r($this->pdoStatement->errorInfo());
                return null;
            }
            else{
                return $mots;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    // récupérer la liste des mots dans un document spécifique
    public function getliste_mots($document)
    {
        $i=0;
        $mot = array();
        $this->pdoStatement = $this->pdo->prepare('SELECT * from document_mot WHERE id_document = :id_document');

        $this->pdoStatement->bindValue(':id_document', $document, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            while ( $mots = $this->pdoStatement->fetch(PDO::FETCH_ASSOC)) {
                $mot[$i]['id_mot']= $mots['id_mot'];
                $i++;
            }
            if($mot === false){
                print_r($this->pdoStatement->errorInfo());
                return null;
            }
            else{
                return $mot;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

     // suppression des documents.
    public function deletfrom_document($document)
    {
        $this->pdoStatement= $this->pdo->prepare('DELETE from document WHERE id_document=:id_document');
        $this->pdoStatement->bindValue(':id_document', $document, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            echo 'ok pour document';
            return true;
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    // suppression de données de la table document mot
    public function deletfrom_document_mot($document)
    {
        $this->pdoStatement= $this->pdo->prepare('DELETE from document_mot WHERE id_document=:id_document');
        $this->pdoStatement->bindValue(':id_document', $document, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            echo 'ok pour documentmot';
            return true;
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    // suppression des mots.
    public function deletfrom_mot($mot)
    {
        $this->pdoStatement= $this->pdo->prepare('DELETE from mot WHERE id_mot=:id_mot');
        $this->pdoStatement->bindValue(':id_mot', $mot, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            echo 'ok pour mot';
            return true;
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }

    public function resultat_a_affiche($idDocument)
    {
        $this->pdoStatement= $this->pdo->prepare('SELECT * from document WHERE id_document=:id_document');
        $this->pdoStatement->bindValue(':id_document', $idDocument, PDO::PARAM_INT);

        $excutIsOk = $this->pdoStatement->execute();

        if($excutIsOk){
            $document = $this->pdoStatement->fetch(PDO::FETCH_ASSOC);
            if($document === false){
                return null;
            }
            else{
                return $document;
            }
        }
        else {
            print_r($this->pdoStatement->errorInfo());
            return false;
        }

    }
}