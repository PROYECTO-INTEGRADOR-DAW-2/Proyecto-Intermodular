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
    } else if($_GET["action"] === "addComment" && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $input = json_decode(file_get_contents('php://input'), true);

        if ($input) {
            $result = $controller->addComment($input);
            echo json_encode($result);
        } else {
            echo json_encode(["error" => true, "errormsg" => "No se han podido leer los datos internamente"]);
        }
        
        
    } else if($_GET["action"] === "editComment" && $_SERVER['REQUEST_METHOD'] === 'PATCH') {

        $input = json_decode(file_get_contents('php://input'), true);

        if ($input) {
            $result = $controller->editComment($input);
            echo json_encode($result);
        } else {
            echo json_encode(["error" => true, "errormsg" => "No se han podido leer los datos internamente"]);
        }
        
        
    } else if($_GET["action"] === "getComments" && isset($_GET["idProduct"])) {
        $comments = $controller->getComments($_GET["idProduct"]);

        echo json_encode($comments);

    } else if($_GET["action"] === "getProduct" && isset($_GET["idProduct"])) {
        $product = $controller->getProduct($_GET["idProduct"]);

        echo json_encode($product);
    } else if($_GET["action"] === "getSessionUser") {
        $user = $controller->getSessionUser();

        echo json_encode($user);
    }
}
?>