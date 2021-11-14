<?php
namespace ManagerBdd;
use PDO;

class ConnexionBdd
{
    public $pdo;

    /**
     * ConnexionBDD constructor.
     */
    public function __construct()
    {
        $servername = 'localhost';
        $username = 'root';
        $password = '';

        //On essaie de se connecter
        try{
            $this->pdo = new PDO("mysql:host=$servername;dbname=indexation", $username, $password);
        }
            /*On capture les exceptions si une exception est lancée et on affiche
             *les informations relatives à celle-ci*/
        catch(PDOException $e){
            echo "Erreur : " . $e->getMessage();
        }
    }
}
