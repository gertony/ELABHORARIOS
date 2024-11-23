<?php
// Incluir la conexión a la base de datos
include_once('../../includes/db.php');

// Incluir validación de sesión
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Variables para almacenar datos
$tabla = null;
$reporte = null;
$registros = [];

// Procesar selección de tablas y reportes
if (isset($_GET['tabla'])) {
    $tabla = $_GET['tabla'];

    if ($tabla == 'registros' && isset($_GET['reporte'])) {
        $reporte = $_GET['reporte'];

        // Reporte para Registros
        if ($reporte == 'listado') {
            $query = $pdo->prepare("
                SELECT c.id AS carga_id, c.id_curso, c.id_docente, d.nombre AS docente_nombre, c.seccion
                FROM cargas_horarias c
                JOIN docentes d ON c.id_docente = d.id
                ORDER BY d.nombre
            ");
        }
    } elseif ($tabla == 'aulas' && isset($_GET['reporte'])) {
        $reporte = $_GET['reporte'];

        // Reporte para Aulas
        if ($reporte == 'horarios_aula') {
            $query = $pdo->prepare("
                SELECT h.id_aula, h.hora_educativa, h.dia_semana, h.id_curso
                FROM horarios h
                WHERE h.id_aula = '1A'
                ORDER BY h.dia_semana, h.hora_educativa
            ");
        } elseif ($reporte == 'carga_dia') {
            $query = $pdo->prepare("
                SELECT h.id_aula, h.dia_semana, COUNT(h.hora_educativa) AS total_horas
                FROM horarios h
                WHERE h.id_aula = '1A'
                GROUP BY h.id_aula, h.dia_semana
                ORDER BY h.dia_semana
            ");
        }
    } elseif ($tabla == 'cursos' && isset($_GET['reporte'])) {
        $reporte = $_GET['reporte'];

        // Reporte para Cursos
        if ($reporte == 'cursos_por_dia') {
            $query = $pdo->prepare("
                SELECT DISTINCT h.id_curso, c.nombre AS curso_nombre, h.dia_semana
                FROM horarios h
                JOIN cursos c ON h.id_curso = c.id
                WHERE h.dia_semana = 'Lun'
                ORDER BY c.nombre
            ");
        }
    } elseif ($tabla == 'docentes' && isset($_GET['reporte'])) {
        $reporte = $_GET['reporte'];

        // Reporte para Docentes
        if ($reporte == 'docentes_aulas') {
            $query = $pdo->prepare("
                SELECT d.id AS docente_id, d.nombre AS docente_nombre, COUNT(DISTINCT h.id_aula) AS total_aulas
                FROM cargas_horarias c
                JOIN docentes d ON c.id_docente = d.id
                JOIN horarios h ON c.id = h.id_carga_horaria
                GROUP BY d.id, d.nombre
                HAVING COUNT(DISTINCT h.id_aula) > 1
            ");
        }
    }

    // Ejecución de la consulta y obtención de registros
    if (isset($query)) {
        $query->execute();
        $registros = $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Información</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function cambiarTabla() {
            const tabla = document.getElementById('tablaSelect').value;
            window.location.href = '?tabla=' + tabla;
        }

        function cambiarReporte() {
            const tabla = document.getElementById('tablaSelect').value;
            const reporte = document.getElementById('reporteSelect').value;
            window.location.href = '?tabla=' + tabla + '&reporte=' + reporte;
        }
    </script>
</head>
<body>

<h1>Seleccionar Reportes</h1>
<button onclick="window.location.href='../home.php'">Menu Principal</button>
<br>
<!-- Desplegable para tablas -->
<label for="tablaSelect">Seleccionar tabla:</label>
<select id="tablaSelect" onchange="cambiarTabla()">
    <option value="" disabled selected>Seleccione una tabla</option>
    <option value="aulas" <?php echo $tabla == 'aulas' ? 'selected' : ''; ?>>Aulas</option>
    <option value="docentes" <?php echo $tabla == 'docentes' ? 'selected' : ''; ?>>Docentes</option>
    <option value="cursos" <?php echo $tabla == 'cursos' ? 'selected' : ''; ?>>Cursos</option>
    <option value="registros" <?php echo $tabla == 'registros' ? 'selected' : ''; ?>>Registros</option>
</select>

<!-- Desplegable para reportes -->
<?php if ($tabla): ?>
    <label for="reporteSelect">Seleccionar reporte:</label>
    <select id="reporteSelect" onchange="cambiarReporte()">
        <option value="" disabled selected>Seleccione un reporte</option>
        <?php if ($tabla == 'aulas'): ?>
            <option value="horarios_aula" <?php echo $reporte == 'horarios_aula' ? 'selected' : ''; ?>>Horarios de Aula</option>
            <option value="carga_dia" <?php echo $reporte == 'carga_dia' ? 'selected' : ''; ?>>Carga por Día</option>
        <?php elseif ($tabla == 'docentes'): ?>
            <option value="docentes_aulas" <?php echo $reporte == 'docentes_aulas' ? 'selected' : ''; ?>>Docentes y Aulas</option>
        <?php elseif ($tabla == 'cursos'): ?>
            <option value="cursos_por_dia" <?php echo $reporte == 'cursos_por_dia' ? 'selected' : ''; ?>>Cursos por Día</option>
        <?php elseif ($tabla == 'registros'): ?>
            <option value="listado" <?php echo $reporte == 'listado' ? 'selected' : ''; ?>>Listado General</option>
        <?php endif; ?>
    </select>
<?php endif; ?>

<!-- Mostrar los registros -->
<h2>Resultados</h2>
<?php if ($registros): ?>
    <table>
        <thead>
        <tr>
            <?php foreach (array_keys($registros[0]) as $columna): ?>
                <th><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $columna))); ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registros as $registro): ?>
            <tr>
                <?php foreach ($registro as $valor): ?>
                    <td><?php echo htmlspecialchars($valor); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay datos disponibles.</p>
<?php endif; ?>
</body>
</html>
