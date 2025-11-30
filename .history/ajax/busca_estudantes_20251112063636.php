<?php
session_start();
require_once("../modelo/crud.php");

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'Docente') {
    echo json_encode([]);
    exit;
}

$docente_id = $_SESSION['id'];
$termo = $_POST['termo'] ?? '';

$crud = new crud();
$conn = $crud->getConexao();

// Buscar curso e departamento do docente
$stmtDocente = $conn->prepare("
    SELECT u.curso_id, u.departamento_id
    FROM usuarios u
    WHERE u.id = ? AND u.tipo = 'Docente'
");
$stmtDocente->execute([$docente_id]);
$infoDocente = $stmtDocente->fetch(PDO::FETCH_ASSOC);

// Se não achar docente, não retorna nada
if (!$infoDocente) {
    echo json_encode([]);
    exit;
}

$cursoDocente = $infoDocente['curso_id'];
$departamentoDocente = $infoDocente['departamento_id'];

// Buscar estudantes que são do mesmo curso e departamento
$stmt = $conn->prepare("
    SELECT id, nome 
    FROM usuarios 
    WHERE tipo = 'Estudante'
      AND ativo = 1
      AND curso_id = ?
      AND departamento_id = ?
      AND nome LIKE ?
    LIMIT 30
");

$stmt->execute([$cursoDocente, $departamentoDocente, "%$termo%"]);
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$resultados = [];
foreach ($estudantes as $est) {
    $resultados[] = [
        'id' => $est['id'],
        'text' => $est['nome']
    ];
}

echo json_encode($resultados);
