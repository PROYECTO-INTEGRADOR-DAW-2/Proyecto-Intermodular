<?php
//Products.php
include 'comment.php';
define("COMMENTSURL","http://localhost:3001/comentaris");

class Comments {

    private $comments;

    public function __construct() {

        $ch = curl_init(COMMENTSURL);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true]);
        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            throw new Exception("Error al obtener los comentarios");
        }

        curl_close($ch);

        $commentsArray = json_decode($response, true);

        $this->comments = array_map(fn($comment) => new Comment(
                    $comment['id'],
                    $comment['id_producte'],
                    $comment['id_usuari'],
                    $comment['comentari'],
                    $comment['valoracion'],
                    $comment['data'])
        , $commentsArray
        );



    }


    public function addComment(array $comment) {

        // Obtener todos los comentarios
        $curl = curl_init(COMMENTSURL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if(!curl_errno($curl)) {
            $comments = json_decode($response, true);

            // Obtener último y sumar ID
            $ultimo = end($comments);
            $comment["id"] = strval($ultimo["id"] + 1);
            

        } else {
            curl_close($curl);
            return ["error" => true, "errormsg" => "Internal Error: Error al obtener el ultimo id de comentario"];
        }

        curl_close($curl);
        

        // POST con el nuevo comentario
        $post = curl_init(COMMENTSURL);
        curl_setopt($post, CURLOPT_POST, true);
        curl_setopt($post, CURLOPT_POSTFIELDS, json_encode($comment));
        curl_setopt($post, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($post, CURLOPT_RETURNTRANSFER, true);

        curl_exec($post);

        if(curl_errno($post)) {
            return ["error" => true, "errormsg" => "Error al añadir el nuevo comentario"];
        }

        curl_close($post);


        // Guardar en local
        $this->comments[] = new Comment(
            $comment['id'],
            $comment['id_producte'],
            $comment['id_usuari'],
            $comment['comentari'],
            $comment['valoracion'],
            $comment['data']
        );

        return ["error" => false, "successmsg" => "Comentario añadido correctamente", "body" => $comment];
    }

    public function editComment(array $comment) {
        $comment['id'] = (int) $comment['id'];
        // 1. Actualizar en BBDD
        $ch = curl_init(COMMENTSURL . "/" . $comment['id']);

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($comment)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                "error" => true,
                "errormsg" => "Error de comunicación al editar el comentario"
            ];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            return [
                "error" => true,
                "errormsg" => "La API devolvió un error al editar el comentario"
            ];
        }

        // 2. Actualizar localmente
        $found = false;

        $this->comments = array_map(function ($c) use ($comment, &$found) {
            if ($c->getId() === $comment['id']) {
                $found = true;
                return new Comment(
                    $comment['id'],
                    $comment['id_producte'],
                    $comment['id_usuari'],
                    $comment['comentari'],
                    $comment['valoracion'],
                    $comment['data']
                );
            }
            return $c;
        }, $this->comments);

        if (!$found) {
            return [
                "error" => true,
                "errormsg" => "Comentario no encontrado localmente"
            ];
        }

        return [
            "error" => false,
            "successmsg" => "Comentario editado correctamente"
        ];
    }

    public function deleteComment(array $comment){
        $comment['id'] = (int) $comment['id'];
        // 1. Eliminar localmente
        $longitudAnterior = count($this->comments);

        $this->comments = array_values(array_filter(
            $this->comments,
            fn($c) => $c->getId() !== $comment['id']
        ));

        if (count($this->comments) === $longitudAnterior) {
            return [
                "error" => true,
                "errormsg" => "No se ha podido encontrar el comentario localmente"
            ];
        }

        // 2. Eliminar en BBDD
        $ch = curl_init(COMMENTSURL . "/" . $comment['id']);

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ]
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                "error" => true,
                "errormsg" => "Error de comunicación con la BBDD"
            ];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            return [
                "error" => true,
                "errormsg" => "La API devolvió un error al eliminar el comentario"
            ];
        }

        return [
            "error" => false,
            "successmsg" => "Se ha eliminado correctamente el comentario"
        ];
    }

    public function getComments($idProduct){

        $url = "http://localhost:3001/comentaris?id_producte=$idProduct";
        
        $peticion = curl_init($url);

        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($peticion);

        if (curl_errno($peticion)) {
            return ["error" => true, "errormsg" => "Error al obtener los comentarios"];
        }

        curl_close($peticion);

        return json_decode($response);

    }

    public function getComment($idComment){
        $url = "http://localhost:3001/comentaris/$idComment";

        $peticion = curl_init($url);
        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($peticion);

        if (curl_errno($peticion)) {
            return ["error" => true, "errormsg" => "Error al obtener el comentario"];
        }

        curl_close($peticion);

        return json_decode($response);

    }
}
?>