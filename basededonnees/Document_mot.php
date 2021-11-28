<?php
namespace Document_mot;

class Document_mot
{
    /**

     * @var int id_document

     */

    private $id_document;

    /**

     * @var int id_mot

     */

    private $id_mot;

    /**

     * @var int poids

     */

    private $poids;

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
     * @return int
     */
    public function getId_mot()
    {
        return $this->id_mot;
    }

    /**
     * @param int $id_mot
     */
    public function setId_mot($id_mot)
    {
        $this->id_mot = $id_mot;
    }

    /**
     * @return int
     */
    public function getPoids()
    {
        return $this->poids;
    }

    /**
     * @param int $poids
     */
    public function setPoids($poids)
    {
        $this->poids = $poids;
    }

}