<?php
//Products.php
include 'producto.php';
define("URL","http://localhost:3001/productos");

class Products {
    public function __construct() {
        $this->products = [];
    }

    public function addProduct(array $product) {

        $peticion = curl_init(URL);

        curl_setopt($peticion, CURLOPT_POST, true);
        curl_setopt($peticion, CURLOPT_POSTFIELDS, http_build_query($product));
        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($peticion);

        if (curl_errno($peticion)) {
            echo "Error: " . curl_error($peticion);
        }

        curl_close($peticion);

        $this->products[] = new Product($product['id'],
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
}
?>