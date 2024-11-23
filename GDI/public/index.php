<?php
// Iniciar sesión
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: home.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']
    $password = $_POST['password'];

    $valid_username = 'paolo123';
    $valid_password = 'skibidi';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;

        header("Location: home.php");
        exit;
    } else {
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
    <link rel="stylesheet" href="index.css">
    <script src="public/js/auth.js" defer></script>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar sesión</h2>
        <img src="src/logo_1.png" alt="Logo del colegio">

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
