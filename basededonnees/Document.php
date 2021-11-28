<?php
namespace  Document;

class Document
{
    /**

     * @var int id_document

     */

    private $id_document;

    /**

     * @var string nom_document

     */

    private $nom_document;

    /**

     * @var string titre_document

     */

    private $titre_document;

    /**

     * @var string description

     */

    private $description;



    /** GETTER AND SETTER */

    /**
     * @return int
     */
    public function getId_document()
    {
        return $this->id_document;
    }

    /**
     * @param int $id_document
     */
    public function setId_document($id_document)
    {
        $this->id_document = $id_document;
    }

    /**
     * @return string
     */
    public function getNom_document()
    {
        return $this->nom_document;
    }

    /**
     * @param string $nom_document
     */
    public function setNom_document($nom_document)
    {
        $this->nom_document = $nom_document;
    }

    /**
     * @return string
     */
    public function getTitre_document()
    {
        return $this->titre_document;
    }

    /**
     * @param string $titre_document
     */
    public function setTitre_document($titre_document)
    {
        $this->titre_document = $titre_document;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

}