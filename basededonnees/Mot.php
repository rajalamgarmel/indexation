<?php
namespace  Mot;

class Mot
{

    /**
     * @var int id_mot
     */

    private $id_mot;

    /**
     * @var string mot
     */

    private $mot;


    /** GETTER AND SETTER */

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
     * @return string
     */
    public function getMot()
    {
        return $this->mot;
    }

    /**
     * @param string $mot
     */
    public function setMot($mot)
    {
        $this->mot = $mot;
    }


}