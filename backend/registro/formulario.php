<?php
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- 0. Saneamiento Inicial ---
    // Usamos htmlspecialchars para limpiar todos los inputs antes de usarlos/validarlos.
    // Esto es crucial para prevenir XSS si mostramos los datos.
    $nombre         = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '';
    $correo         = isset($_POST['correo']) ? htmlspecialchars(trim($_POST['correo'])) : '';
    $ciclo          = isset($_POST['ciclo']) ? htmlspecialchars(trim($_POST['ciclo'])) : '';
    $telefono       = isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : '';
    $consentimiento = isset($_POST['consentimiento']) ? 'Sí' : 'No'; // Lo ajustamos para el guardado

    // --- 1. Validar campos de texto básicos (No Vacíos) ---
    if (empty($nombre)) {
        $errores[] = "Falta el campo: Nombre";
    }
    if (empty($correo)) {
        $errores[] = "Falta el campo: Correo";
    }
    if (empty($ciclo)) {
        $errores[] = "Falta el campo: Ciclo";
    }
    // El campo teléfono lo validaremos más abajo, pero también debe ser obligatorio
    if (empty($telefono)) {
        $errores[] = "Falta el campo: Teléfono";
    }

    // --- 2. Validar Checkbox (Consentimiento) ---
    // Si la variable $consentimiento es 'No', es que no se marcó.
    if ($consentimiento === 'No') {
        $errores[] = "Debes aceptar el consentimiento.";
    }

    // --- 3. Validar tipo de dato Teléfono (Usando ctype_digit) ---
    if (!empty($telefono) && !ctype_digit($telefono)) {
        $errores[] = "El teléfono debe contener solo dígitos (0-9).";
    }

    // --- 4. Validar Correo (Email Válido) ---
    if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo proporcionado no es válido.";
    }
    
    // 5. Procesar respuesta
    if (!empty($errores)) {
        echo "<h3>🚨 Errores encontrados:</h3><ul>";
        foreach ($errores as $error) {
            echo "<li style='color:red'>" . htmlspecialchars($error) . "</li>"; 
        }
        echo "</ul>";
        echo "<a href='../../frontend/registro.html'>Volver al formulario</a>";
    } else {
        // --- Guardado de datos ---
        
        // Crear carpeta si no existe
        if (!is_dir('./files')) { 
            // 0777 permite permisos de lectura, escritura y ejecución para todos
            mkdir('./files', 0777, true); 
        }

        $ficheroUsuarios = fopen("./files/users.txt", "a+");
        
        // Datos a guardar (Usamos las variables saneadas/limpias)
        $linea = "Nombre: " . $nombre . " | " .
                 "Correo: " . $correo . " | " .
                 "Ciclo: " . $ciclo . " | " .
                 "Tel: " . $telefono . " | " .
                 "Consentimiento: " . $consentimiento . PHP_EOL; // $consentimiento es 'Sí' o 'No'

        fwrite($ficheroUsuarios, $linea);
        fclose($ficheroUsuarios);

        echo "<p style='color:green'>✅ ¡Éxito! Datos guardados correctamente.</p>";
    }
} else {
    // Si intentan entrar directo a este archivo sin enviar formulario
    header('Location: formulario.html');
    exit;
}
?>