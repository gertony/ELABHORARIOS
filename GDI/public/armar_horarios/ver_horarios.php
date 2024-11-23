<?php
include_once('../../includes/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

$queryAulas = $pdo->prepare("SELECT id, CONCAT(grado, seccion) AS aula FROM aulas ORDER BY grado, seccion");
$queryAulas->execute();
$aulas = $queryAulas->fetchAll(PDO::FETCH_ASSOC);

$dias = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie'];
$horas = range(1, 9);

$horariosAsignados = [];
if (isset($_GET['aula'])) {
    $aulaSeleccionada = $_GET['aula'];

    $queryHorarios = $pdo->prepare("
        SELECT h.dia_semana, h.hora_educativa, c.nombre AS curso
        FROM horarios h
        LEFT JOIN cursos c ON h.id_curso = c.id
        WHERE h.id_aula = :aula
    ");
    $queryHorarios->execute([':aula' => $aulaSeleccionada]);
    $horariosAsignados = $queryHorarios->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios Creados</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function mostrarHorarios() {
            const aula = document.getElementById('aula').value;
            if (!aula) return;

            window.location.href = `ver_horarios.php?aula=${aula}`;
        }
    </script>
</head>
<body>
<h1>Horarios Creados</h1>
<button onclick="window.location.href='../home.php'">Menu Principal</button>
<br>
<label for="aula">Seleccionar Aula:</label>
<select id="aula" name="aula" onchange="mostrarHorarios()">
    <option value="">- Seleccionar Aula -</option>
    <?php foreach ($aulas as $aula): ?>
        <option value="<?php echo $aula['id']; ?>" <?php echo isset($aulaSeleccionada) && $aulaSeleccionada == $aula['id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($aula['aula']); ?>
        </option>
    <?php endforeach; ?>
</select>

<?php if (isset($aulaSeleccionada)): ?>
    <table>
        <thead>
        <tr>
            <th>Hora</th>
            <?php foreach ($dias as $dia): ?>
                <th><?php echo $dia; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($horas as $hora): ?>
            <tr>
                <td><?php echo $hora; ?></td>
                <?php foreach ($dias as $dia): ?>
                    <td>
                        <?php
                        $horario = array_filter($horariosAsignados, function($h) use ($dia, $hora) {
                            return $h['dia_semana'] == $dia && $h['hora_educativa'] == $hora;
                        });

                        if ($horario) {
                            $curso = reset($horario);
                            echo htmlspecialchars($curso['curso']);
                        } else {
                            echo "Campo libre";
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='armar_horarios.php'">Armar Horarios</button>

    <button onclick="window.location.href='exportar_pdf.php?aula=<?php echo $aulaSeleccionada; ?>'">Exportar a PDF</button>

    <button onclick="if(confirm('¿Estás seguro de que deseas eliminar todos los horarios de esta aula?')) { window.location.href = 'eliminar_horarios.php?aula=<?php echo $aulaSeleccionada; ?>'; }">Reiniciar Horario</button>


<?php endif; ?>

</body>
</html>
