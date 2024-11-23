<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Gestión de Horarios</title>
    <link rel="stylesheet" href="public/styles.css">
    <script src="public/js/auth.js" defer></script>
</head>
<body>
    <div class="home-container">
        <h2>Bienvenido, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Seleccione una opción:</p>

        <div class="buttons-container">
            <a href="editar_info/editar_info.php" class="btn">Editar Información</a>
            <a href="ver_reportes/ver_reportes.php" class="btn">Ver Reportes</a>
            <a href="armar_horarios/ver_horarios.php" class="btn">Armar Horarios</a>
        </div>

        <form action="home.php" method="post">
            <button type="submit" name="logout" class="btn logout-btn">Cerrar sesión</button>
        </form>
    </div>
</body>
</html>
