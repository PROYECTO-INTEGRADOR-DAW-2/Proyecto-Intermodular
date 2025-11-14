<?php

session_start();

define('users_url', 'http://localhost:3001/usuaris');

$error = '';
$success = '';
$user_data = null; 

// 1. Identificar l'usuari 
// Comprovem si 'user_id' existeix a la sessió
if (!isset($_SESSION['user_id'])) {
    // Si no existeix, no està autenticat. El fem fora.
    // Assumim que la teva pàgina de login es diu 'login.php'
    header('Location: login.php');
    exit; 
}

// Si hem arribat aquí, l'usuari està identificat.
$user_id = $_SESSION['user_id'];
$api_url_user = users_url . '/' . $user_id; 


// 3. Permetre actualitzar 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recollim i netegem dades del formulari
    $nom = trim($_POST['nom']);
    $cognoms = trim($_POST['cognoms']);
    $email = trim($_POST['email']);

    // Validacions bàsiques 
    if (empty($nom) || empty($cognoms) || empty($email)) {
        $error = "Error: Els camps Nom, Cognoms i Email són obligatoris.";
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $error = "Error: El format de l'email no és vàlid.";
    } else {
        
        // --- Validació correcta, preparem el PATCH ---

        // Dades que enviarem. Només el que es pot canviar.
        $data_to_patch = [
            "nom" => $nom,
            "cognoms" => $cognoms,
            "email" => $email
        ];

        $payload = json_encode($data_to_patch);

        // Configurem la petició PATCH
        $options = [
            'http' => [
                'method'  => 'PATCH', // Important: mètode PATCH
                'header'  => "Content-Type: application/json\r\n" .
                             "Content-Length: " . strlen($payload) . "\r\n",
                'content' => $payload,
                'ignore_errors' => true // Per llegir la resposta d'error
            ]
        ];

        $context = stream_context_create($options);
        
        // Enviem la petició PATCH a la URL de l'usuari
        $response_patch = @file_get_contents($api_url_user, false, $context);

        if ($response_patch === false || !strpos($http_response_header[0], "200 OK")) {
            $error = "Error: No s'han pogut actualitzar les dades. Resposta: " . $http_response_header[0];
        } else {
            $success = "Dades actualitzades correctament!";

        }
    }
}


// 2.  Mostrar la informació 

$response_get = @file_get_contents($api_url_user);

if ($response_get === false) {
    // Si falla el GET, potser l'usuari de la sessió ja no existeix
    $error = "Error: No s'ha pogut carregar el perfil de l'usuari ID: " . $user_id;
    // Hauríem de destruir la sessió aquí
    session_destroy();
} else {
    $user_data = json_decode($response_get, true);
    

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($error)) {
        $user_data['nom'] = $_POST['nom'];
        $user_data['cognoms'] = $_POST['cognoms'];
        $user_data['email'] = $_POST['email'];
    }
}

?>
<!DOCTYPE html>
<html lang="ca">
    
<head>
    <meta charset="UTF-8">
    <title>Perfil d'Usuari</title>
    <link rel="stylesheet" href="styleProfile.css">
</head>
<body>

    <a href="logout.php" style="float: right;">Tancar Sessió</a>

    <h2>Perfil d'Usuari</h2>

    <?php
    if (!empty($error)) {
        echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
    }
    if (!empty($success)) {
        echo "<p style='color:green;'>" . htmlspecialchars($success) . "</p>";
    }
    ?>

    <?php if ($user_data):  ?>
    
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <div>
                <label>Nom d'Usuari (no editable):</label>
                <input type_text" 
                       value="<?php echo htmlspecialchars($user_data['nom_usuari']); ?>" 
                       disabled>
            </div>
            <div>
                <label>Data de Registre (no editable):</label>
                <input type="text" 
                       value="<?php echo htmlspecialchars($user_data['data_registre']); ?>" 
                       disabled>
            </div>

            <hr>
            
            <div>
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" 
                       value="<?php echo htmlspecialchars($user_data['nom']); ?>" 
                       required>
            </div>
            <div>
                <label for="cognoms">Cognoms:</label>
                <input type="text" id="cognoms" name="cognoms" 
                       value="<?php echo htmlspecialchars($user_data['cognoms']); ?>" 
                       required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user_data['email']); ?>" 
                       required>
            </div>
            <div>
                <button type="submit">Guardar Canvis</button>
            </div>
        </form>
        
    <?php else: ?>
        <p>No s'ha pogut carregar la informació de l'usuari.</p>
    <?php endif; ?>

</body>
</html>