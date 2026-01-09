<?php

//Comment.php
class Comment {

    private $id;
    private $id_producte;
    private $id_usuari;
    private $comentari;
    private $valoracion;
    private $data;

    public function __construct($id, 
    $id_producte, 
    $id_usuari, 
    $comentari, 
    $valoracion, 
    $data
    )  {
        $this->id = $id;
        $this->id_producte = $id_producte;
        $this->id_usuari = $id_usuari;
        $this->comentari = $comentari;
        $this->valoracion = $valoracion;
        $this->data = $data;
    }

    public function getId() : int {
        return $this->id;
    } 

}



?>