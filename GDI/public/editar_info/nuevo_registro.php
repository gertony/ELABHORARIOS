<?php
include_once('../../includes/db.php');

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$tabla = $_GET['tabla'] ?? null;
$id = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $grado = $_POST['grado'] ?? '';
    $seccion = $_POST['seccion'] ?? '';
    $horas = $_POST['horas'] ?? '';
    $nivel = $_POST['nivel'] ?? '';

    if (empty($id)) {
        $error = "Por favor, complete todos los campos obligatorios.";
    } else {
        try {
            if ($tabla == 'cursos') {
                $query = $pdo->prepare("INSERT INTO cursos (id, nombre, grado, numero_horas) VALUES (:id, :nombre, :grado, :horas)");
                $query->execute(['id'=>$id, 'nombre' => $nombre, 'grado' => $grado, 'horas' => $horas]);
            } elseif ($tabla == 'docentes') {
                if (empty($id)) {
                    $error = "El DNI del docente es obligatorio.";
                } else {
                    $query = $pdo->prepare("INSERT INTO docentes (id, nombre, horas_semanales) VALUES (:id, :nombre, :horas)");
                    $query->execute(['id' => $id, 'nombre' => $nombre, 'horas' => $horas]);
                }
            } elseif ($tabla == 'aulas') {
                $query = $pdo->prepare("INSERT INTO aulas (id, grado, seccion, nivel) VALUES (:id, :grado, :seccion, :nivel)");
                $query->execute(['id' => $id, 'grado' => $grado, 'seccion' => $seccion, 'nivel' => $nivel]);
            }

            header('Location: editar_info.php?tabla=' . $tabla);
            exit();

        } catch (PDOException $e) {
            $error = "Error al insertar el registro: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Registro</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<h1>Añadir Nuevo <?php echo ucfirst($tabla); ?></h1>

<?php if (isset($error)) { ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php } ?>

<form method="POST">
    <?php if ($tabla == 'cursos') { ?>

        <label for="id">Codigo del Curso:</label>
        <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" required><br><br>

        <label for="nombre">Nombre del Curso:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="grado">Grado:</label>
        <input type="text" id="grado" name="grado" required><br><br>

        <label for="horas">Número de Horas:</label>
        <input type="number" id="horas" name="horas" required><br><br>

    <?php } elseif ($tabla == 'docentes') { ?>

        <label for="id">DNI del Docente:</label>
        <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" required><br><br>

        <label for="nombre">Nombre del Docente:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="horas">Horas Semanales:</label>
        <input type="number" id="horas" name="horas" required><br><br>

    <?php } elseif ($tabla == 'aulas') { ?>

        <label for="id">ID del Aula:</label>
        <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" required><br><br>

        <label for="grado">Grado:</label>
        <input type="text" id="grado" name="grado" required><br><br>

        <label for="seccion">Sección:</label>
        <input type="text" id="seccion" name="seccion" required><br><br>

        <label for="nivel">Nivel:</label>
        <input type="text" id="nivel" name="nivel" required><br><br>

    <?php } ?>

    <button type="submit">Añadir Registro</button>
</form>

</body>
</html>
