<?php

    $errores = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = ['nombre', 'correo', 'ciclo', 'telefono', 'consentimiento'];
    
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            $errores[] = "<p style='color:red'>Falta el campo: $campo</p>";
        }

        if($campo === "telefono") {
            if(!is_int($var)) {
                $errores[] = "<p style='color:red'>El telefono debe de ser un entero</p>";
            }
        }
    }

    if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "<p style='color:red'>El correo no es válido</p>";
    }

    if(!empty($errores)) {
        echo "<ul>";

        foreach($errores as $error) {
            echo "<li> $error </li>";
        }

        echo "</ul>";
    } else {
        $ficheroUsuarios = fopen("files/users", "a+");
        $campos = [
            'nombre' => $_POST['nombre'], 
            'correo' => $_POST['correo'], 
            'ciclo' => $_POST['ciclo'], 
            'telefono' => $_POST['telefono'], 
            'consentimiento' => $_POST['consentimiento']
        ];
        
        foreach($campos as $campo => $valor) {
            fwrite($ficheroUsuarios, $campo . ":" . $valor . PHP_EOL);
        }

        fclose($ficheroUsuarios);
    }
    
}

    


?>
