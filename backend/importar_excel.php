<?php
    require("../vendor/autoload.php");
    use PhpOffice\PhpSpreadsheet\IOFactory;
    $productos = [];
    $tiposAjuste = ["Ajustado", "Holgado"];
    $tallasValidas = ["S","M","L","XL"];
    

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

                        case "A":
                            $filaAsociativa["Categoria"] = (string) $celda->getValue();
                            break;

                        case "B":
                            $filaAsociativa["Nombre"] = (string) $celda->getValue();
                            break;

                        case "C":
                            $filaAsociativa["Precio"] = (float) $celda->getValue();
                            break;

                        case "D":   // Tamaño
                            $valor = $celda->getValue();

                            // ✔ Si es número (excel puede enviarlo como int o float)
                            if (is_numeric($valor)) {
                                $filaAsociativa["Talla"] = (string)$valor;
                                break;
                            }

                            // ✔ Si no es número → comprobar si está en tallas válidas
                            if (in_array($valor, $tallasValidas)) {
                                $filaAsociativa["Talla"] = $valor;
                                break;
                            }

                            // ❌ Si llega aquí → error
                            echo "Error: la talla '$valor' no es válida";
                            die();
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
