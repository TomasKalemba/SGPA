<?php
require_once("../modelo/crud.php");
session_start();

$crud = new crud();
$conn = $crud->getConexao();

$termo = $_POST['termo'] ?? '';
$docente_id = $_SESSION['id']; // docente logado

// Busca departamento do docente
$stmtDepto = $conn->prepare("SELECT departamento_id FROM usuarios WHERE id = ?");
$stmtDepto->execute([$docente_id]);
$departamento_id = $stmtDepto->fetchColumn();

if (!$departamento_id) {
    echo json_encode([]);
    exit;
}

// Busca estudantes do mesmo departamento cujo nome corresponde ao termo
$stmt = $conn->prepare("
    SELECT id, nome AS text 
    FROM usuarios 
    WHERE tipo = 'Estudante' 
      AND departamento_id = ? 
      AND nome LIKE ?
    ORDER BY nome ASC
    LIMIT 20
");
$stmt->execute([$departamento_id, "%$termo%"]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);
