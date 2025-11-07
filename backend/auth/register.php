<?php
// URL de la API (JSON Server) donde se guardan los usuarios
define('users_url', 'http://localhost:3001/usuaris');

// Variables para guardar mensajes de estado
$error = ''; 
$EnviadoCorrecto = ''; 

// 1. Comprobar si el formulario se ha enviado 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Recoger y limpiar datos del formulario
    $nom_usuari = trim($_POST['nom_usuari']);
    $email = trim($_POST['email']);
    $contrasenya = trim($_POST['contrasenya']);
    $nom = trim($_POST['nom']);
    $cognoms = trim($_POST['cognoms']);

    // --- INICIO DE VALIDACIONES ---

    // 3. Validar campos vacíos
    if (empty($nom_usuari) || empty($email) || empty($contrasenya) || empty($nom) || empty($cognoms)) {
        $error = "Error: Todos los campos son obligatorios.";
    
    // 4. Validar formato de email
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $error = "Error: El formato del email no es válido.";
    
    // 5. Validar longitud de contraseña
    } else if (strlen($contrasenya) < 8) {
        $error = "Error: La contraseña debe tener al menos 8 caracteres.";

    } else {
        // --- SI TODAS LAS VALIDACIONES SON CORRECTAS ---

        // 6. Preparar URL para consultar duplicados
        $check_url = users_url . '?nom_usuari=' . urlencode($nom_usuari);
        
        // Ejecutar la consulta GET 
        $response_check = file_get_contents($check_url);

        if ($response_check === false) {
            $error = "Error: No se pudo conectar con la API para verificar el usuario.";
        } else {
            // Convertir la respuesta JSON (del GET) a un array PHP
            $existing_users = json_decode($response_check, true);

            // 7. Comprobar si el array de usuarios NO está vacío
            if (!empty($existing_users)) {
                $error = "Error: El nombre de usuario '{$nom_usuari}' ya existe.";
            } else {
                
                // --- El usuario NO existe, se puede registrar ---

                // 8. Cifrar la contraseña 
                $contrasenya_cifrada = password_hash($contrasenya, PASSWORD_DEFAULT);

                // 9. Preparar el array de datos para enviar a la API
                $data = [
                    "nom_usuari" => $nom_usuari,
                    "contrasenya" => $contrasenya_cifrada, // Se envía la cifrada
                    "email" => $email,
                    "nom" => $nom,
                    "cognoms" => $cognoms,
                    "data_registre" => date('c') // Fecha en formato ISO
                ];

                // Convertir el array PHP a formato JSON
                $payload = json_encode($data);

                // 10. Configurar la petición POST para enviar el JSON
                $options = [
                    'http' => [
                        'method'  => 'POST', // Indicar que es un método POST
                        'header'  => "Content-Type: application/json\r\n" .
                                     "Content-Length: " . strlen($payload) . "\r\n",
                        'content' => $payload, // El JSON que vamos a enviar
                        'ignore_errors' => true // Para poder leer la respuesta aunque sea un error
                    ]
                ];

                // Crear el "contexto" para la petición
                $context = stream_context_create($options);
                
                // 11. Enviar los datos (POST) a la API
                $response_post = file_get_contents(users_url, false, $context);

                if ($response_post === false) {
                    $error = "Error: No se pudo enviar los datos a JSON.";
                } else {
                    // 12. Comprobar si JSON Server confirma la creación (código 201)
                    // $http_response_header es una variable especial de PHP
                    if (strpos($http_response_header[0], "201 Created")) {
                        $EnviadoCorrecto = "Usuario registrado";
                    } else {
                        // Si la API devuelve otro estado (ej: 500, 404)
                        $error = "Error: El JSON devolvió un estado erroneo" . $http_response_header[0];
                    }
                }
            }
        }
    }
}
// --- FIN DEL BLOQUE PHP ---
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registre Usuari</title>
</head>
<body>

    <h2>Registre Usuari </h2>

    <?php
    // Sección para mostrar mensajes de error al usuario
    if (!empty($error)) {
        echo "<p >" . htmlspecialchars($error) . "</p>";
    }
    // Sección para mostrar mensajes de éxito al usuario
    if (!empty($EnviadoCorrecto)) {
        echo "<p>" . htmlspecialchars($EnviadoCorrecto) . "</p>";
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label for="nom_usuari">Nom dUsuari:</label>
            <input type="text" id="nom_usuari" name="nom_usuari" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="contrasenya">Contrasenya:</label>
            <input type="password" id="contrasenya" name="contrasenya" required>
        </div>
        <div>
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div>
            <label for="cognoms">Cognoms:</label>
            <input type="text" id="cognoms" name="cognoms" required>
        </div>
        <div>
            <button type="submit">Registrar</button>
        </div>
    </form>

</body>
</html>