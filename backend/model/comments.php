<?php
//Products.php
include 'comment.php';
define("URL","http://localhost:3001/comments");

class Comments {

    private $comments;

    public function __construct() {
        $this->comments = [];
    }

    public function addComment(array $comment) {

        // Obtener todos los productos
        $curl = curl_init(URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if(!curl_errno($curl)) {
            $comments = json_decode($response, true);

            // Obtener último y sumar ID
            $ultimo = end($comments);
            $comment["id"] = strval($ultimo["id"] + 1);

        } else {
            echo "Error GET: ".curl_error($curl);
            return;
        }

        curl_close($curl);


        // POST con el nuevo producto
        $post = curl_init(URL);
        curl_setopt($post, CURLOPT_POST, true);
        curl_setopt($post, CURLOPT_POSTFIELDS, http_build_query($comment));
        curl_setopt($post, CURLOPT_RETURNTRANSFER, true);

        curl_exec($post);

        if(curl_errno($post)) {
            echo "Error POST: ".curl_error($post);
        }

        curl_close($post);


        // Guardar en local
        $this->comments[] = new Comment(
            $comment['id'],
            $comment['id_producte'],
            $comment['id_usuari'],
            $comment['commentari'],
            $comment['valoracion'],
            $comment['data']
        );
    }


    public function getComments(){
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

    public function getComment($idComment){
        $url = "http://localhost:3001/productos/$idComment";

        $peticion = curl_init($url);
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