<?php
include_once('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aula = $_POST['aula'] ?? null;
    $horarios = $_POST['horario'] ?? [];

    if (!$aula) {
        echo json_encode(['success' => false, 'message' => 'Aula no seleccionada']);
        exit();
    }

    try {
        // Validar si el aula existe
        $queryAula = $pdo->prepare("SELECT id FROM aulas WHERE id = :aula");
        $queryAula->execute([':aula' => $aula]);
        if (!$queryAula->fetchColumn()) {
            throw new Exception('El aula seleccionada no existe.');
        }

        // Recorrer todos los horarios enviados y solo insertar los que tengan valores
        foreach ($horarios as $dia => $horas) {
            foreach ($horas as $hora => $curso) {
                // Solo insertar si hay un valor de curso seleccionado (no vacío)
                if (!empty($curso)) {
                    // Obtener el id_carga_horaria para el curso seleccionado
                    $queryCargaHoraria = $pdo->prepare("
                        SELECT id 
                        FROM cargas_horarias 
                        WHERE id_curso = :curso 
                        AND id_docente = (SELECT id_docente FROM cargas_horarias WHERE id_curso = :curso LIMIT 1) 
                        LIMIT 1
                    ");
                    $queryCargaHoraria->execute([':curso' => $curso]);
                    $id_carga_horaria = $queryCargaHoraria->fetchColumn();

                    if (!$id_carga_horaria) {
                        throw new Exception('No se encontró una carga horaria para el curso seleccionado.');
                    }

                    // Ahora insertamos en la tabla horarios
                    $query = $pdo->prepare("
                        INSERT INTO horarios (dia_semana, hora_educativa, id_curso, id_aula, id_carga_horaria)
                        VALUES (:dia, :hora, :curso, :aula, :id_carga_horaria)
                        ON DUPLICATE KEY UPDATE id_curso = :curso
                    ");
                    $query->execute([
                        ':dia' => $dia,
                        ':hora' => $hora,
                        ':curso' => $curso,
                        ':aula' => $aula,
                        ':id_carga_horaria' => $id_carga_horaria,
                    ]);
                }
            }
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
