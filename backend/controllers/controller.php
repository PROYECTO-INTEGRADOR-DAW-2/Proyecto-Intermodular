
<?php
//Controller.php

include '../model/products.php';
include '../model/;'

class Controller {

    private $products;

    public function __construct() {
        $this->products = new Products();
    }

    public function getProducts() {
        
        return $this->products->getProducts();
        
    }

    public function getComments($idProduct) {
        
        return $this->products->getComments($idProduct);
        
    }

    public function getComment($idProduct) {
        
        return $this->products->getProduct($idProduct);
        
    }

    public function addProduct($product) {

        $result = $this->products->addProduct($product);
        echo json_encode(["status" => "success", "message" => "Product added", "data" => $result]);
    }

}

?>