<?php
session_start();
require_once("../modelo/crud.php");

// DEBUG TEMPORÁRIO (vamos remover depois)
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]); 
    exit;
}

$docente_id = $_SESSION['id'];
$termo = $_POST['termo'] ?? '';

$crud = new crud();
$conn = $crud->getConexao();

if (empty($termo)) {
    echo json_encode([]);
    exit;
}

/*
  1) descobrir o departamento e curso do docente
*/
$sqlDoc = "
    SELECT d.departamento_id, d.curso_id
    FROM docentes d
    WHERE d.id = ?
";
$stmt = $conn->prepare($sqlDoc);
$stmt->execute([$docente_id]);
$docInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$docInfo) {
    echo json_encode([]);
    exit;
}

$departamento_id = $docInfo['departamento_id'];
$curso_id = $docInfo['curso_id'];

/*
  2) buscar estudantes que combinam com docente
*/
$sql = "
    SELECT u.id, u.nome
    FROM usuarios u
    INNER JOIN estudantes e ON e.id = u.id
    WHERE u.tipo = 'Estudante'
      AND e.departamento_id = ?
      AND e.curso_id = ?
      AND u.nome LIKE ?
    LIMIT 20
";

$stmt = $conn->prepare($sql);
$stmt->execute([$departamento_id, $curso_id, "%$termo%"]);
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$resultados = [];
foreach ($estudantes as $est) {
    $resultados[] = [
        'id' => $est['id'],
        'text' => $est['nome']
    ];
}

echo json_encode($resultados);
