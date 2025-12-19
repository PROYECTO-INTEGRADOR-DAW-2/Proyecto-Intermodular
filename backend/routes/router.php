<?php
//Router.php
include '../controllers/controller.php';

$controller = new Controller();

if(isset($_GET["action"])) {

    if($_GET["action"] === "getProducts") {

        $products = $controller->getProducts();
        echo json_encode($products);

    } else if($_GET["action"] === "addProduct" && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $input = json_decode(file_get_contents('php://input'), true);

        if ($input) {
            $controller->addProduct($input);
        }
    } else if($_GET["action"] === "getComments" && isset($_GET["idProduct"])) {
        $product = $controller->getComments($_GET["idProduct"]);

        echo json_encode($product);
    }
}
?>