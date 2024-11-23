<?php
// Incluir la conexión a la base de datos
include_once('../../includes/db.php');

// Incluir validación de sesión (asegúrate de que el usuario está logueado)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php'); // Redirige a login si no está logueado
    exit();
}

// Inicializar la variable para los registros
$registros = [];

// Consultar dependiendo de la tabla seleccionada
if (isset($_GET['tabla'])) {
    $tabla = $_GET['tabla'];

    if ($tabla == 'cursos') {
        // Consulta para obtener todos los cursos desde la base de datos
        $query = $pdo->prepare("SELECT * FROM cursos");
        $query->execute();
        $registros = $query->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($tabla == 'docentes') {
        // Consulta para obtener todos los docentes desde la base de datos
        $query = $pdo->prepare("SELECT * FROM docentes");
        $query->execute();
        $registros = $query->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($tabla == 'aulas') {
        // Consulta para obtener todos los aulas desde la base de datos
        $query = $pdo->prepare("SELECT * FROM aulas");
        $query->execute();
        $registros = $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Eliminar un registro
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($tabla == 'cursos') {
        $query = $pdo->prepare("DELETE FROM cursos WHERE id = :id");
    } elseif ($tabla == 'docentes') {
        $query = $pdo->prepare("DELETE FROM docentes WHERE id = :id");
    } elseif ($tabla == 'aulas') {
        $query = $pdo->prepare("DELETE FROM aulas WHERE id = :id");
    }
    $query->execute(['id' => $id]);

    header('Location: editar_info.php?tabla=' . $tabla); // Redirige después de eliminar
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Información</title>
    <link rel="stylesheet" href="editar_info.css">
    <script>
        // Cambiar el contenido según la opción seleccionada
        function cambiarTabla() {
            var select = document.getElementById('tablaSelect');
            var selectedValue = select.value;
            window.location.href = '?tabla=' + selectedValue;
        }
    </script>
</head>
<body>

    <h1>Selecciona una tabla para ver</h1>
    <button onclick="window.location.href='../home.php'">Menu Principal</button> <br>

    <!-- Desplegable para seleccionar qué mostrar -->
    <select id="tablaSelect" onchange="cambiarTabla()">
        <option value="cursos" <?php echo isset($_GET['tabla']) && $_GET['tabla'] == 'cursos' ? 'selected' : ''; ?>>Cursos</option>
        <option value="docentes" <?php echo isset($_GET['tabla']) && $_GET['tabla'] == 'docentes' ? 'selected' : ''; ?>>Docentes</option>
        <option value="aulas" <?php echo isset($_GET['tabla']) && $_GET['tabla'] == 'aulas' ? 'selected' : ''; ?>>Aulas</option>
    </select>

    <h2>Listado de <?php echo ucfirst($tabla); ?></h2>
    <a href="nuevo_registro.php?tabla=<?php echo $tabla; ?>"><button>Añadir Nuevo</button></a>

    <table>
        <thead>
            <tr>
                <?php
                if ($tabla == 'cursos') {
                    echo "<th>ID</th><th>Grado</th><th>Nombre</th><th>Horas</th><th>Acciones</th>";
                } elseif ($tabla == 'docentes') {
                    echo "<th>ID</th><th>Nombre</th><th>Horas Contratado</th><th>Acciones</th>";
                } elseif ($tabla == 'aulas') {
                    echo "<th>ID</th><th>Grado</th><th>Sección</th><th>Nivel</th><th>Acciones</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Mostrar los datos según la tabla seleccionada
            foreach ($registros as $item) {
                echo "<tr>";
                if ($tabla == 'cursos') {
                    echo "<td>" . htmlspecialchars($item['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['grado']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['numero_horas']) . "</td>";
                    echo "<td><a href='editar_registro.php?id=" . $item['id'] . "&tabla=cursos'>Editar</a> | <a href='?tabla=" . $tabla . "&accion=eliminar&id=" . $item['id'] . "' onclick='return confirm(\"¿Estás seguro de eliminar este registro?\")'>Eliminar</a></td>";
                } elseif ($tabla == 'docentes') {
                    echo "<td>" . htmlspecialchars($item['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['horas_semanales']) . "</td>";
                    echo "<td><a href='editar_registro.php?id=" . $item['id'] . "&tabla=docentes'>Editar</a> | <a href='?tabla=" . $tabla . "&accion=eliminar&id=" . $item['id'] . "' onclick='return confirm(\"¿Estás seguro de eliminar este registro?\")'>Eliminar</a></td>";
                } elseif ($tabla == 'aulas') {
                    echo "<td>" . htmlspecialchars($item['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['grado']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['seccion']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['nivel']) . "</td>";
                    echo "<td><a href='editar_registro.php?id=" . $item['id'] . "&tabla=aulas'>Editar</a> | <a href='?tabla=" . $tabla . "&accion=eliminar&id=" . $item['id'] . "' onclick='return confirm(\"¿Estás seguro de eliminar este registro?\")'>Eliminar</a></td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
