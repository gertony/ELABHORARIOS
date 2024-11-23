<?php
include_once('../../includes/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['aula'])) {
    $aulaSeleccionada = $_GET['aula'];

    $queryEliminar = $pdo->prepare("DELETE FROM horarios WHERE id_aula = :aula");
    $queryEliminar->execute([':aula' => $aulaSeleccionada]);

    header('Location: ver_horarios.php?aula=' . $aulaSeleccionada);
    exit();
}
?>
<?php
