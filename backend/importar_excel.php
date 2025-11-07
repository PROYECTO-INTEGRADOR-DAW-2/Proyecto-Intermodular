<?php
    require("../vendor/autoload.php");
    use PhpOffice\PhpSpreadsheet\IOFactory;
    $productos = [];
    $tiposAjuste = ["Ajustado", "Holgado"];

    function importar_datos($rutaFichero) {

        try {
            $documento = IOFactory::load($rutaFichero);

            $hoja = $documento::getActiveSheet();

            foreach($hoja->getRowIterator() as $fila) {

                $celdaIterator = $fila->getCellIterator();
                $celdaIterator->setIterateOnlyExistingCells(false);
                $filaAsociativa = ["Categoria" => "", "Nombre" => "", "Precio" => 0.0, "Talla" => "", "Color" => "", "Stock" => 0, "Ajuste" => ""];
                
                foreach($celdaIterator as $celda) {
                    switch ($celda->getColumn()) {
                        case "A" : $filaAsociativa["Categoria"] = (string)$celda->getValue();
                    }
                }
            }

        } catch(Exception $err) {

        }



    }


    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (is_uploaded_file($_FILES['ficheroExcel']['tmp_name'])) {

            if ($_FILES['ficheroExcel']['error']) {
                echo "Error en el fichero";
                die();
            }
            
            $fichero = $_FILES['ficheroExcel']['name'];
            $infoFichero = pathinfo($fichero);

            if ($infoFichero['extension'] === 'xlsx' || $infoFichero['extension'] === 'xls' || $infoFichero['extension'] === 'csv'){

                foreach (glob("../uploads/*") as $archivo) {
                    if (is_file($archivo)) {
                        unlink($archivo); 
                    }
                }

                move_uploaded_file($_FILES['ficheroExcel']['tmp_name'], "../uploads/products." . $infoFichero['extension']);
                echo "Fichero subido";
                importar_datos("../uploads/products." . $infoFichero['extension']);
                
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
        <form action="envioDatos.php" method="post" enctype="multipart/form-data">
            <input type="file" name="ficheroExcel">
            <input type="submit" name="enviarDatos">
        </form>
    </section>
</body>
</html>
