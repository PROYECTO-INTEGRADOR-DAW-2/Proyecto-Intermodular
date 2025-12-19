<?php
require("./vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\IOFactory;

// ==========================================
// 1. CONSTANTES Y CONFIGURACIÓN
// ==========================================
define('PRODUCTS_URL', "http://localhost:3001/productos");
define('CONFIG_VALIDACION', [
    'ajustes'    => ["ajustado", "holgado"],
    'alturas'    => ["alto", "bajo", "normal", ""],
    'tallas'     => ["s", "m", "l", "xl"],
    'deportes'   => ["trail"],
    'categorias' => ["zapatillas", "camisetas", "pantalones"],
    'sexos'      => ["h", "m", "hombre", "mujer"]
]);

// ==========================================
// 2. FUNCIONES AUXILIARES
// ==========================================

/**
 * Obtiene todos los IDs actuales de la API para evitar duplicados.
 */
function obtenerIdsExistentes() {
    $ch = curl_init(PRODUCTS_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    curl_close($ch);

    $datos = json_decode($respuesta, true);
    $ids = [];

    if (is_array($datos)) {
        foreach ($datos as $prod) {
            $ids[] = $prod['id'];
        }
    }
    return $ids;
}

/**
 * Limpia el precio eliminando símbolos y normalizando decimales.
 */
function limpiarPrecio($valor) {
    $limpio = preg_replace('/[^\d,.]/', '', $valor);
    return str_replace(',', '.', $limpio);
}

/**
 * Función principal de importación
 */
function importar_datos($rutaFichero) {
    $productosNuevos = [];
    $idsLocales = obtenerIdsExistentes(); // Cargar IDs de la BD
    $saltarPrimeraLinea = true;

    try {
        $documento = IOFactory::load($rutaFichero);
        $hoja = $documento->getActiveSheet();

        echo "<div class='console'>";
        echo "<h3>Procesando archivo...</h3>";

        foreach($hoja->getRowIterator() as $fila) {
            // Saltar encabezados
            if ($saltarPrimeraLinea) {
                $saltarPrimeraLinea = false;
                continue;
            }

            $celdaIterator = $fila->getCellIterator();
            $celdaIterator->setIterateOnlyExistingCells(false);

            // Estructura base del producto
            $prod = [
                "id" => 0, "Categoria" => "", "Nombre" => "", "Precio" => 0.0,
                "Talla" => "", "Color" => "", "Stock" => 0, "Ajuste" => "",
                "Sexo" => "", "Descripcion" => "", "Altura" => "", "Deporte" => "", "Oferta" => "", "Img" => ""
            ];

            // --------------------------------------------------
            // LECTURA DE COLUMNAS
            // --------------------------------------------------
            foreach($celdaIterator as $celda) {
                $val = trim((string)$celda->getValue());
                $col = $celda->getColumn();

                switch ($col) {
                    case "A": // ID
                        if (is_numeric($val)) $prod["id"] = (int)$val;
                        else continue 3; // Si no hay ID válido, saltamos a la siguiente fila del Excel
                        break;

                    case "B": // Categoría
                        if (in_array(strtolower($val), CONFIG_VALIDACION['categorias'])) $prod["Categoria"] = $val;
                        else dieError("Categoría inválida: $val");
                        break;

                    case "C": // Nombre
                        $prod["Nombre"] = $val;
                        break;

                    case "D": // Precio
                        $precio = limpiarPrecio($val);
                        if (is_numeric($precio)) $prod["Precio"] = (float)$precio;
                        else dieError("Precio inválido: $val");
                        break;

                    case "E": // Talla
                        if (is_numeric($val) || in_array(strtolower($val), CONFIG_VALIDACION['tallas'])) $prod["Talla"] = $val;
                        else dieError("Talla inválida: $val");
                        break;

                    case "F": // Color
                        $prod["Color"] = $val;
                        break;

                    case "G": // Stock
                        if (is_numeric($val)) $prod["Stock"] = (int)$val;
                        else dieError("Stock inválido: $val");
                        break;

                    case "H": // Ajuste
                        if (in_array(strtolower($val), CONFIG_VALIDACION['ajustes'])) $prod["Ajuste"] = $val;
                        else dieError("Ajuste inválido: $val");
                        break;

                    case "I": // Altura
                        if (in_array(strtolower($val), CONFIG_VALIDACION['alturas'])) $prod["Altura"] = $val;
                        else dieError("Altura inválida: $val");
                        break;

                    case "J": // Deporte
                        if (in_array(strtolower($val), CONFIG_VALIDACION['deportes'])) $prod["Deporte"] = $val;
                        else dieError("Deporte inválido: $val");
                        break;

                    case "K": // Oferta
                        if (in_array(strtolower($val), ["no", "yes"])) $prod["Oferta"] = $val;
                        else dieError("Oferta inválida (yes/no): $val");
                        break;

                    case "L": // Sexo
                        if (in_array(strtolower($val), CONFIG_VALIDACION['sexos'])) $prod["Sexo"] = $val;
                        else dieError("Sexo inválido: $val");
                        break;

                    case "M": // Descripción
                        $prod["Descripcion"] = $val;
                        break;
                    case "N";
                        $prod["Img"] = $val;
                        break;
                }
            }

            // --------------------------------------------------
            // VERIFICACIÓN DE DUPLICADOS
            // --------------------------------------------------
            if (in_array($prod["id"], $idsLocales)) {
                echo "<p class='warning'>⚠ El ID <strong>{$prod['id']}</strong> ya existe. Saltando...</p>";
                continue; // Pasa a la siguiente fila del Excel
            }

            // Si es nuevo, lo añadimos a la lista de procesados local y al array final
            $idsLocales[] = $prod["id"];
            $productosNuevos[] = $prod;
        }

        // --------------------------------------------------
        // SUBIDA A LA API
        // --------------------------------------------------
        if (empty($productosNuevos)) {
            echo "<p class='info'>No hay productos nuevos para importar.</p>";
        } else {
            foreach($productosNuevos as $producto) {
                enviarProductoAPI($producto);
            }
        }
        echo "</div>"; // Fin consola

    } catch(Exception $err) {
        dieError("Excepción crítica: " . $err->getMessage());
    }
}

function enviarProductoAPI($producto) {
    $ch = curl_init(PRODUCTS_URL);
    $jsonData = json_encode($producto);
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    curl_exec($ch);
    
    if(curl_errno($ch)) {
        echo "<p class='error'>❌ Error CURL ID {$producto['id']}: " . curl_error($ch) . "</p>";
    } else {
        echo "<p class='success'>✅ Producto ID <strong>{$producto['id']}</strong> ({$producto['Nombre']}) importado.</p>";
    }
    curl_close($ch);
}

function dieError($msg) {
    die("<p class='error'>⛔ $msg</p>");
}

// ==========================================
// 3. LÓGICA DEL FORMULARIO (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_FILES['ficheroExcel']) && is_uploaded_file($_FILES['ficheroExcel']['tmp_name'])) {
        
        $ext = strtolower(pathinfo($_FILES['ficheroExcel']['name'], PATHINFO_EXTENSION));
        $permitidos = ['xlsx', 'xls', 'csv'];

        if (in_array($ext, $permitidos)){
            // Limpiar carpeta uploads
            array_map('unlink', glob("../uploads/*"));
            if (!is_dir("../uploads")) mkdir("../uploads", 0777, true);
            
            $ruta = "../uploads/products." . $ext;
            
            if(move_uploaded_file($_FILES['ficheroExcel']['tmp_name'], $ruta)) {
                importar_datos($ruta);
            } else { 
                dieError("Error al mover el fichero al servidor."); 
            }
        } else { 
            dieError("Formato inválido. Solo se admite: " . implode(", ", $permitidos)); 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importador de Productos</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 2rem auto; padding: 0 1rem; line-height: 1.6; }
        .console { background: #f4f4f4; padding: 15px; border-radius: 5px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto; margin-bottom: 20px;}
        .success { color: green; margin: 5px 0; }
        .error { color: red; font-weight: bold; margin: 5px 0; }
        .warning { color: orange; margin: 5px 0; }
        .info { color: #555; font-style: italic; }
        form { background: #eef; padding: 20px; border-radius: 8px; }
        input[type="submit"] { background: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; }
        input[type="submit"]:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Importar Productos (Excel)</h1>
    
    <section>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="file"><strong>Selecciona el archivo Excel:</strong></label><br><br>
            <input type="file" name="ficheroExcel" id="file" required accept=".csv, .xlsx, .xls">
            <br><br>
            <input type="submit" value="🚀 Subir e Importar">
        </form>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="file"><strong>Añade las imagenes</strong></label><br><br>
            <input type="file" name="imagenesProductos" id="imagenesProductos">
            <br><br>
            <input type="submit" value="🚀 Subir e Importar">
        </form>
    </section>
</body>
</html>