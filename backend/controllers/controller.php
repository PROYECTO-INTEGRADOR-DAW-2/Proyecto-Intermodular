
<?php
//Controller.php

include '../model/products.php';
include '../model/comments.php';
session_start();
class Controller {

    private $products;
    private $comments;

    public function __construct() {
        $this->products = new Products();
        $this->comments = new Comments();
    }

    public function getProducts() {
        
        return $this->products->getProducts();
        
    }

    public function getComments($idProduct) {
        
        return $this->comments->getComments($idProduct);
        
    }

    public function addComment($comment) {

        if(!isset($_SESSION['user_id'])) {
            $errorMsg = ["error" => true,
                "errormsg" => "Debes iniciar sesion para poder comentar"            
            ];
            return $errorMsg;
        }

    
        $result = $this->comments->addComment($comment);
        return $result;
    }

    public function editComment($comment) {

        if(!isset($_SESSION['user_id'])) {
            $errorMsg = ["error" => true,
                "errormsg" => "Debes iniciar sesion para poder editar el comentario"            
            ];
            return $errorMsg;
        }

    
        $result = $this->comments->editComment($comment);
        return $result;
    }

    public function getProduct($idProduct) {
        
        return $this->products->getProduct($idProduct);
        
    }

    public function addProduct($product) {

        $result = $this->products->addProduct($product);
        echo json_encode(["status" => "success", "message" => "Product added", "data" => $result]);
    }

    public function getSessionUser() {
        if(!isset($_SESSION['user_id'])) {

            $errorMsg = ["error" => true,
                "errormsg" => "No se ha iniciado sesion"
            ];
            return $errorMsg;

        } else {
            $user_id = $_SESSION['user_id'];
            $url = "http://localhost:3001/usuaris/$user_id";
            $peticion = curl_init($url);
        
            curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($peticion);

            if (curl_errno($peticion)) {
                
                $errorMsg = ["error" => true,
                    "errormsg" => "Error al intentar acceder a los datos de usuario"
                ];

                return $errorMsg;
            }

            curl_close($peticion);

            return json_decode($response);
        }
    }

}

?>