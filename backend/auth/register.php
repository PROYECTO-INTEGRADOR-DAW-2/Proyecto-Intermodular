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

       $response_all = file_get_contents(users_url);

    if ($response_all === false) {
        $error = "Error: No se pudo conectar con la API.";
    } else {
        $all_users = json_decode($response_all, true);
        
        $usuario_duplicado = false;
        $max_id = 0; // Iniciar el ID máximo

        // 7. Recorrer usuarios para verificar duplicados y encontrar MAX ID
        foreach ($all_users as $user) {
            
            // Comprobar duplicado (ignorando mayúsculas/minúsculas)
            if (isset($user['nom_usuari']) && strtolower($user['nom_usuari']) === strtolower($nom_usuari)) {
                $usuario_duplicado = true;
            }
            
            // Encontrar el ID numérico más alto
            if (isset($user['id']) && is_numeric($user['id']) && $user['id'] > $max_id) {
                $max_id = (int)$user['id'];
            }
        }

        // 7b. Comprobar si se encontró duplicado
        if ($usuario_duplicado) {
            $error = "Error: El nombre de usuario '{$nom_usuari}' ya existe.";
        } else {
            
            // --- El usuario NO existe, se puede registrar ---

            // 8. Calcular el nuevo ID (Max ID + 1)
            $nuevo_id = (string)($max_id + 1);

            // 8b. Cifrar la contraseña 
            $contrasenya_cifrada = password_hash($contrasenya, PASSWORD_DEFAULT);

            // 9. Preparar el array de datos para enviar a la API (INCLUYENDO EL ID)
            $data = [
                "id" => $nuevo_id, // <-- ¡Importante! Enviamos el ID numérico
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