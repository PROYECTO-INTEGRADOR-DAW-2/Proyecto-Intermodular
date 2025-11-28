
<?php
//Controller.php

include '../model/products.php';

class Controller {

    public function __construct() {
        $this->products = new Products();
    }

    public function getProducts() {
        $products = $this->products->getProducts();
        echo json_encode($products);
    }

    public function addProduct($product) {
        $result = $this->products->addProduct($product);
        echo json_encode(["status" => "success", "message" => "Product added", "data" => $result]);
    }

}

?>