<?php
// Incluir conexión y validación de sesión
include_once('../../includes/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Obtener aulas
$queryAulas = $pdo->prepare("SELECT id, CONCAT(grado, seccion) AS aula FROM aulas ORDER BY grado, seccion");
$queryAulas->execute();
$aulas = $queryAulas->fetchAll(PDO::FETCH_ASSOC);

// Inicializar días y horas
$dias = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie'];
$horas = range(1, 9);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armar Horarios</title>
    <link rel="stylesheet" href="armar_horarios.css">
    <script>
        // Función para cargar cursos dinámicamente según el aula seleccionada
        function cargarCursos() {
            const aula = document.getElementById('aula').value;
            if (!aula) return;

            // Actualizar el campo oculto con el id del aula seleccionado
            document.getElementById('id_aula').value = aula;

            fetch(`obtener_cursos.php?aula=${aula}`)
                .then(response => response.json())
                .then(data => {
                    const selects = document.querySelectorAll('select[name^="horario"]');
                    selects.forEach(select => {
                        select.innerHTML = `<option value="">- Seleccionar -</option>`;
                        data.forEach(curso => {
                            const option = document.createElement('option');
                            option.value = curso.id;
                            option.textContent = curso.nombre;
                            select.appendChild(option);
                        });
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        // Guardar el horario
        function guardarHorario() {
            const formData = new FormData(document.getElementById('formHorario'));
            fetch('guardar_horario.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Horario guardado correctamente');
                        location.reload();
                    } else {
                        alert('Error al guardar horario: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body>
<h1>Armar Horarios</h1>

<!-- Selector de aula -->
<label for="aula">Seleccionar Aula:</label>
<select id="aula" name="aula" onchange="cargarCursos()">
    <option value="">- Seleccionar Aula -</option>
    <?php foreach ($aulas as $aula): ?>
        <option value="<?php echo $aula['id']; ?>">
            <?php echo htmlspecialchars($aula['aula']); ?>
        </option>
    <?php endforeach; ?>
</select>

<!-- Campo oculto para enviar el aula seleccionada -->
<form id="formHorario">
    <input type="hidden" id="id_aula" name="aula" value="">

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
                        <select name="horario[<?php echo $dia; ?>][<?php echo $hora; ?>]">
                            <option value="">- Seleccionar -</option>
                            <!-- Las opciones se actualizarán dinámicamente -->
                        </select>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button class="btn-guardar" type="button" onclick="guardarHorario()">Guardar Horarios</button>
    <a href="ver_horarios.php"><button type="button">Volver a Ver Horarios</button></a>
</form>
</body>
</html>
