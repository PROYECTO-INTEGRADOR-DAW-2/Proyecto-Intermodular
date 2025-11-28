<?php
//Router.php
include '../controllers/controller.php';

$controller = new Controller();

if(isset($_GET["action"])) {
    if($_GET["action"] === "getProducts") {
        $controller->getProducts();
    } elseif($_GET["action"] === "addProduct" && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            $controller->addProduct($input);
        }
    }
}
?>