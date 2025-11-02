<?php
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (is_uploaded_file($_FILES['ficheroExcel']['tmp_name'])) {

            if ($_FILES['ficheroExcel']['error']) {
                echo "Error en el fichero";
                die();
            }

            $fichero = $_FILES['ficheroExcel']['name'];
            $infoFichero = pathinfo($fichero);

            if ($infoFichero['extension'] === 'xlsx' || $infoFichero['extension'] === 'xls' || $infoFichero['extension'] === 'csv'){
                move_uploaded_file($_FILES['ficheroExcel']['tmp_name'], "../../uploads");
                echo "Fichero subido";
            } else {
                echo "Error en el formato, solo se permiten los formatos xls,xlsx,csv";
                die();
            }

        } else {
            echo "Error en la subida del fichero";
            die();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir datos</title>
</head>
<body>
    <section>
        <form action="envioDatos.php" method="post">
            <input type="file" name="ficheroExcel">
            <input type="submit" name="enviarDatos">
        </form>
    </section>
</body>
</html>
