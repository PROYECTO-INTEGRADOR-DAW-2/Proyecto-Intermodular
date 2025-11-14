<?php
// 0. Iniciar la sessió SEMPRE a l'inici del script
session_start(); 

define('users_url', 'http://localhost:3001/usuaris');

// Variables per a missatges
$error = '';
// $success ja no és necessari, perquè redirigim

// 1. Comprovar si el formulari s'ha enviat (Mètode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recollir i netejar dades
    $nom_usuari = trim($_POST['nom_usuari']);
    $contrasenya = trim($_POST['contrasenya']);

    // Validar camps buits
    if (empty($nom_usuari) || empty($contrasenya)) {
        $error = "Error: Has d'omplir tots els camps.";
    } else {
        
        // 2. Comprovar si l'usuari existeix 
        $check_url = users_url . '?nom_usuari=' . urlencode($nom_usuari);
        
        $response_get = @file_get_contents($check_url); 

        if ($response_get === false) {
            $error = "Error: No s'ha pogut connectar amb la API.";
        } else {
            $usuaris = json_decode($response_get, true);

            // 3. Comprovar si l'array d'usuaris NO està buit
            if (!empty($usuaris)) {
                
                $usuari = $usuaris[0]; 

                // 4. Validar la contrasenya
                if (password_verify($contrasenya, $usuari['contrasenya'])) {
                    
                    // 5. ÈXIT: Usuari i contrasenya correctes
                    $_SESSION['user_id'] = $usuari['id'];
                    $_SESSION['user_name'] = $usuari['nom_usuari'];
                    setcookie('user_id', $usuari['id'], time() + 3600, "/");

                    // Redirigim a profile.php
                    header('Location: profile.php');
                    exit; 

                } else {
                    $error = "Error: La contrasenya no és correcta.";
                }
            } else {
                $error = "Error: L'usuari '" . htmlspecialchars($nom_usuari) . "' no existeix.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Inici de Sessió</title>
    
    <link rel="stylesheet" href="styleLogin.css">
    
</head>
<body>

    <div class="container">
        <h2>Inici de Sessió</h2>

        <?php
        // Secció per mostrar missatges (ara amb classes CSS)
        if (!empty($error)) {
            echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label for="nom_usuari">Nom d'Usuari:</label>
                <input type="text" id="nom_usuari" name="nom_usuari" required>
            </div>
            <div>
                <label for="contrasenya">Contrasenya:</label>
                <input type="password" id="contrasenya" name="contrasenya" required>
            </div>
            <div>
                <button type="submit">Entrar</button>
            </div>
        </form>
    </div>

</body>
</html>