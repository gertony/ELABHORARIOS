<?php
include_once('../../includes/db.php');

if (!isset($_GET['aula'])) {
    echo json_encode([]);
    exit();
}

$aula = $_GET['aula'];

// Obtener el grado del aula
$queryGrado = $pdo->prepare("SELECT grado FROM aulas WHERE id = :aula");
$queryGrado->execute([':aula' => $aula]);
$grado = $queryGrado->fetchColumn();

if (!$grado) {
    echo json_encode([]);
    exit();
}

// Obtener cursos del grado
$queryCursos = $pdo->prepare("
    SELECT id, nombre 
    FROM cursos 
    WHERE grado = :grado
    ORDER BY nombre
");
$queryCursos->execute([':grado' => $grado]);
$cursos = $queryCursos->fetchAll(PDO::FETCH_ASSOC);

// Devolver los cursos en formato JSON
echo json_encode($cursos);
