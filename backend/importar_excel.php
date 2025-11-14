<?php
    require("./vendor/autoload.php");
    use PhpOffice\PhpSpreadsheet\IOFactory;

    define('tiposAjuste', ["ajustado", "holgado"]);
    define('alturasValidas', ["alto", "bajo", "normal",""]);
    define('tallasValidas', ["s","m","l","xl"]);
    define('deportesValidos', ["trail"]);
    define('categoriasValidas', ["zapatillas","camisetas","pantalones"]);
    define('sexosValidos', ["h","m","hombre","mujer"]);
    define('PRODUCTS_URL', "http://localhost:3001/productos");
    
    

    function importar_datos($rutaFichero) {

        $productos = [];
        $saltarPrimeraLinea = true;
        try {
            $documento = IOFactory::load($rutaFichero);

            $hoja = $documento->getActiveSheet();

            foreach($hoja->getRowIterator() as $fila) {

                if ($saltarPrimeraLinea) {
                    $saltarPrimeraLinea = false;  
                    continue;                     
                }

                $celdaIterator = $fila->getCellIterator();
                $celdaIterator->setIterateOnlyExistingCells(false);
                $filaAsociativa = ["id" => 0, "Categoria" => "", "Nombre" => "", "Precio" => 0.0, "Talla" => "", "Color" => "", "Stock" => 0, "Ajuste" => "", "Sexo" => "" ,"Descripcion" => ""];
                
                foreach($celdaIterator as $celda) {
                    switch ($celda->getColumn()) {
                        case "A":
                            $valor = trim($celda->getValue());

                            if(is_int((int)$valor)) {
                                $filaAsociativa["id"] = (string)$valor;
                                break;
                            }

                            echo("El id asignado no es entero");
                            die();

                        case "B":
                            $valor = trim((string)$celda->getValue());

                            if(in_array(strtolower($valor), categoriasValidas)) {
                                $filaAsociativa["Categoria"] = $valor;
                                break;
                            }
                            echo("La categoria asignada no es valida");
                            die();
                            

                        case "C":
                            $filaAsociativa["Nombre"] = trim((string) $celda->getValue());
                            break;

                        case "D":
                            $valor = (float)(trim($celda->getValue()));

                            if(is_float($valor)) {
                                $filaAsociativa["Precio"] = (float) $celda->getValue();
                            } else {
                                echo("Error en tipo de datos");
                            }
                            break;

                        case "E":   // Tamaño
                            $valor = $celda->getValue();

                            // ✔ Si es número (excel puede enviarlo como int o float)
                            if (is_int((int)$valor)) {
                                $filaAsociativa["Talla"] = $valor;
                                break;
                            }

                            // ✔ Si no es número → comprobar si está en tallas válidas
                            if (in_array(strtolower((string)$valor), tallasValidas)) {
                                $filaAsociativa["Talla"] = $valor;
                                break;
                            }

                            // ❌ Si llega aquí → error
                            echo "Error: la talla '$valor' no es válida";
                            die();

                        case "F": 
                            $filaAsociativa["Color"] = trim((string)$celda->getValue());
                            break;

                        case "G":
                            $valor = (int)$celda->getValue();

                            if(is_int($valor)) {
                                $filaAsociativa["Stock"] = $valor;
                                break;
                            }

                            echo "Error el stock no es valido";
                            die();
                            
                        case "H": 
                            $valor = trim((string)$celda->getValue());

                            if(in_array(strtolower($valor), tiposAjuste)) {
                                $filaAsociativa["Ajuste"] = $valor;
                                break;
                            } 

                            echo "Error en el tipo de ajuste asignado";
                            die();
                            
                        case "I":
                            $valor = trim((string)$celda->getValue());

                            if (in_array(strtolower($valor), alturasValidas)) {
                                $filaAsociativa["Altura"] = $valor;
                                break;
                            }

                            echo("Error en la altura de la zapatilla asignada");
                            die();
                        
                        case "J": 
                            $valor = trim((string)$celda->getValue());

                            if (in_array(strtolower($valor), deportesValidos)) {
                                $filaAsociativa["Deporte"] = $valor;
                                break;
                            }

                            echo("Error en el deporte asignado");
                            die();
                        
                        case "K":
                            $valor = trim((string)$celda->getValue());
                            if(strtolower($valor) === "no" || strtolower($valor) === "yes") {
                                $filaAsociativa["Oferta"] = $valor;
                                break;
                            }

                            echo("Error en la oferta");
                            die();

                        case "L": 
                            $valor = trim((string)$celda->getValue());

                            if (in_array(strtolower($valor), sexosValidos)) {
                                $filaAsociativa["Sexo"] = $valor;
                                break;
                            }

                            echo("Error en el sexo asignado");
                            die();
                            
                        case "M":
                            $valor = trim((string)$celda->getValue());
                            $filaAsociativa["Descripcion"] = $valor;
                            break;
                    }
                }

                $productos[] = $filaAsociativa;

                

                
            }

            foreach($productos as $producto) {
                    $ch = curl_init(PRODUCTS_URL);

                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($producto));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json'
                    ]);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $respuesta = curl_exec($ch);
    
                    if(curl_errno($ch)) {
                        echo "Error: " . curl_error($ch);
                    } else {
                        echo "Producto subidos";
                    }
    
                    curl_close($ch);
                }

            return $productos;
            

        } catch(Exception $err) {
            echo ($err);
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
                $productos = importar_datos("../uploads/products." . $infoFichero['extension']);
                
                foreach($productos as $producto) {
                    foreach ($producto as $key => $value) {
                        echo("<p>$key = $value</p><br>");
                    }
                }
                
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
        <form action="importar_excel.php" method="post" enctype="multipart/form-data">
            <input type="file" name="ficheroExcel">
            <input type="submit" name="enviarDatos">
        </form>
    </section>
</body>
</html>
