<?php

// Incluir la librería TCPDF
require_once('../../tcpdf/tcpdf.php');  // Ajusta la ruta según donde esté la librería TCPDF

// Incluir conexión y validación de sesión
include_once('../../includes/db.php');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit();
}

// Obtener el aula seleccionada
$aulaSeleccionada = isset($_GET['aula']) ? $_GET['aula'] : null;
if (!$aulaSeleccionada) {
    die('No se ha seleccionado un aula.');
}

// Inicializar días y horas
$dias = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie'];
$horas = range(1, 9);

// Obtener los horarios asignados para el aula
$queryHorarios = $pdo->prepare("
    SELECT h.dia_semana, h.hora_educativa, c.nombre AS curso
    FROM horarios h
    LEFT JOIN cursos c ON h.id_curso = c.id
    WHERE h.id_aula = :aula
");
$queryHorarios->execute([':aula' => $aulaSeleccionada]);
$horariosAsignados = $queryHorarios->fetchAll(PDO::FETCH_ASSOC);

// Crear nuevo PDF con orientación horizontal (landscape)
$pdf = new TCPDF('L', 'mm', 'A4');  // 'L' para horizontal, 'mm' para milímetros, 'A4' tamaño de página
$pdf->AddPage();

// Título
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Horario del Aula: ' . $aulaSeleccionada, 0, 1, 'C');

// Tabla de horarios
$pdf->SetFont('helvetica', '', 12);

// Cabecera de la tabla
$pdf->Cell(20, 10, 'Hora', 1, 0, 'C');
foreach ($dias as $dia) {
    $pdf->Cell(40, 10, $dia, 1, 0, 'C');
}
$pdf->Ln();

// Contenido de la tabla
foreach ($horas as $hora) {
    $pdf->Cell(20, 10, $hora, 1, 0, 'C');
    foreach ($dias as $dia) {
        // Buscar el curso asignado para el día y hora
        $horario = array_filter($horariosAsignados, function($h) use ($dia, $hora) {
            return $h['dia_semana'] == $dia && $h['hora_educativa'] == $hora;
        });

        if ($horario) {
            $curso = reset($horario); // Obtener el primer (y único) elemento
            $pdf->MultiCell(40, 10, $curso['curso'], 1, 'C', 0, 0, '', '', true);
        } else {
            $pdf->MultiCell(40, 10, 'Campo libre', 1, 'C', 0, 0, '', '', true);
        }
    }
    $pdf->Ln();
}

// Salida del PDF
$pdf->Output('horario_aula_' . $aulaSeleccionada . '.pdf', 'D');  // Asegúrate de que el segundo parámetro sea 'D' para la descarga

exit();
