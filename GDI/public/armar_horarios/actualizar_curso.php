<?php
// Incluir conexión y validación de sesión
include_once('../../includes/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar si los datos están presentes
    if (isset($data['aula'], $data['dia'], $data['hora'], $data['curso'])) {
        $aula = $data['aula'];
        $dia = $data['dia'];
        $hora = $data['hora'];
        $curso = $data['curso'];

        // Actualizar el curso en la base de datos
        try {
            $query = $pdo->prepare("
                UPDATE horarios
                SET id_curso = :curso
                WHERE id_aula = :aula AND dia_semana = :dia AND hora_educativa = :hora
            ");
            $query->execute([
                ':curso' => $curso,
                ':aula' => $aula,
                ':dia' => $dia,
                ':hora' => $hora
            ]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    }
}
?>
