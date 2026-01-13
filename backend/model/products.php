<?php
//Products.php
include 'producto.php';
define("URL","http://localhost:3001/productos");

class Products {

    private $products;

    public function __construct() {
        $this->products = [];
    }

    public function addProduct(array $product) {

        // Obtener todos los productos
        $curl = curl_init(URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if(!curl_errno($curl)) {
            $productos = json_decode($response, true);

            // Obtener último y sumar ID
            $ultimo = end($productos);
            $product["id"] = strval($ultimo["id"] + 1);

        } else {
            echo "Error GET: ".curl_error($curl);
            return;
        }

        curl_close($curl);



        // POST con el nuevo producto
        $post = curl_init(URL);
        curl_setopt($post, CURLOPT_POST, true);
        curl_setopt($post, CURLOPT_POSTFIELDS, json_encode($product));
        curl_setopt($post, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($post, CURLOPT_RETURNTRANSFER, true);

        curl_exec($post);

        if(curl_errno($post)) {
            echo "Error POST: ".curl_error($post);
        }

        curl_close($post);


        // Guardar en local
        $this->products[] = new Product(
            $product['id'],
            $product['categoria'],
            $product['nombre'],
            $product['precio'],
            $product['talla'],
            $product['color'],
            $product['stock'],
            $product['ajuste'],
            $product['sexo'],
            $product['descripcion'],
            $product['altura'],
            $product['deporte'],
            $product['oferta']
        );
    }


    public function getProducts(){
        $peticion = curl_init();
        curl_setopt($peticion, CURLOPT_URL, URL);
        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($peticion);

        if (curl_errno($peticion)) {
            echo "Error: " . curl_error($peticion);
        }

        curl_close($peticion);

        return json_decode($response);

    }

    public function getProduct($idProduct){
        $url = "http://localhost:3001/productos/$idProduct";

        $peticion = curl_init($url);
        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($peticion);

        if (curl_errno($peticion)) {
            return ["error" => true, "errormsg" => "Error al obtener el producto"];
        }

        curl_close($peticion);

        return json_decode($response);

    }
}
?>