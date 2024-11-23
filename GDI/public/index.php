<?php
// Iniciar sesión
session_start();

// Verificar si el administrador ya está logueado
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Si está logueado, redirigir al home
    header("Location: home.php");
    exit;
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar las credenciales del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Definir usuario y contraseña estáticos (puedes cambiar estos valores)
    $valid_username = 'paolo123';
    $valid_password = 'skibidi';

    // Verificar las credenciales
    if ($username === $valid_username && $password === $valid_password) {
        // Si las credenciales son correctas, iniciar la sesión
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;

        // Redirigir al home
        header("Location: home.php");
        exit;
    } else {
        // Si las credenciales son incorrectas, mostrar un mensaje de error
        $error_message = 'Credenciales incorrectas, por favor intente nuevamente.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestión de Horarios</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="public/js/auth.js" defer></script>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar sesión</h2>

        <?php if (isset($error_message)): ?>
            <div id="error-message" class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <label for="username">Usuario:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>
