<?php
include_once('../../includes/db.php');

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$id = $_GET['id'] ?? null;
$tabla = $_GET['tabla'] ?? null;
$registro = null;

if ($id && $tabla) {
    if ($tabla == 'cursos') {
        $query = $pdo->prepare("SELECT * FROM cursos WHERE id = :id");
        $query->execute(['id' => $id]);
        $registro = $query->fetch(PDO::FETCH_ASSOC);
    } elseif ($tabla == 'docentes') {
        $query = $pdo->prepare("SELECT * FROM docentes WHERE id = :id");
        $query->execute(['id' => $id]);
        $registro = $query->fetch(PDO::FETCH_ASSOC);
    } elseif ($tabla == 'aulas') {
        $query = $pdo->prepare("SELECT * FROM aulas WHERE id = :id");
        $query->execute(['id' => $id]);
        $registro = $query->fetch(PDO::FETCH_ASSOC);
    }
}

if (!$registro) {
    header('Location: editar_info.php?tabla=' . $tabla);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $grado = $_POST['grado'] ?? '';
    $seccion = $_POST['seccion'] ?? '';
    $horas = $_POST['horas'] ?? '';
    $capacidad = $_POST['capacidad'] ?? '';
    $nivel = $_POST['nivel'] ?? '';

    if ($tabla == 'cursos') {
        $query = $pdo->prepare("UPDATE cursos SET nombre = :nombre, grado = :grado, numero_horas = :horas WHERE id = :id");
        $query->execute(['nombre' => $nombre, 'grado' => $grado, 'horas' => $horas, 'id' => $id]);
    } elseif ($tabla == 'docentes') {
        $query = $pdo->prepare("UPDATE docentes SET nombre = :nombre, horas_semanales = :horas WHERE id = :id");
        $query->execute(['nombre' => $nombre, 'horas' => $horas, 'id' => $id]);
    } elseif ($tabla == 'aulas') {
        $query = $pdo->prepare("UPDATE aulas SET grado = :grado, seccion = :seccion, nivel = :nivel WHERE id = :id");
        $query->execute(['grado' => $grado, 'seccion' => $seccion, 'nivel' => $nivel, 'id' => $id]);
    }

    header('Location: editar_info.php?tabla=' . $tabla);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="editar_registro.css">
</head>
<body>

    <h1>Editar <?php echo ucfirst($tabla); ?></h1>

    <form method="POST">
        <?php if ($tabla == 'cursos') { ?>
            <label for="nombre">Nombre del Curso:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($registro['nombre']); ?>" required><br><br>

            <label for="grado">Grado:</label>
            <input type="text" id="grado" name="grado" value="<?php echo htmlspecialchars($registro['grado']); ?>" required><br><br>

            <label for="horas">Número de Horas:</label>
            <input type="number" id="horas" name="horas" value="<?php echo htmlspecialchars($registro['numero_horas']); ?>" required><br><br>

        <?php } elseif ($tabla == 'docentes') { ?>
            <label for="nombre">Nombre del Docente:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($registro['nombre']); ?>" required><br><br>

            <label for="horas">Horas Semanales:</label>
            <input type="number" id="horas" name="horas" value="<?php echo htmlspecialchars($registro['horas_semanales']); ?>" required><br><br>

        <?php } elseif ($tabla == 'aulas') { ?>
            <label for="grado">Grado:</label>
            <input type="text" id="grado" name="grado" value="<?php echo htmlspecialchars($registro['grado']); ?>" required><br><br>

            <label for="seccion">Sección:</label>
            <input type="text" id="seccion" name="seccion" value="<?php echo htmlspecialchars($registro['seccion']); ?>" required><br><br>

            <label for="nivel">Nivel:</label>
            <input type="text" id="nivel" name="nivel" value="<?php echo htmlspecialchars($registro['nivel']); ?>" required><br><br>

        <?php } ?>

        <button type="submit">Actualizar</button>
    </form>

</body>
</html>
