<?php
require_once("../modelo/crud.php");
session_start();

$crud = new crud();
$conn = $crud->getConexao();

$termo = $_POST['termo'] ?? '';
$docente_id = $_SESSION['id']; // docente logado
$excluir_ids = $_POST['excluir_ids'] ?? []; // IDs de estudantes a excluir da busca

// Busca departamento do docente
$stmtDepto = $conn->prepare("SELECT departamento_id FROM usuarios WHERE id = ?");
$stmtDepto->execute([$docente_id]);
$departamento_id = $stmtDepto->fetchColumn();

if (!$departamento_id) {
    echo json_encode([]);
    exit;
}

// Prepara filtro de exclusão
$excluir_sql = '';
$params = [$departamento_id, "%$termo%"];

if (!empty($excluir_ids)) {
    $placeholders = implode(',', array_fill(0, count($excluir_ids), '?'));
    $excluir_sql = " AND id NOT IN ($placeholders)";
    $params = array_merge($params, $excluir_ids);
}

// Busca estudantes do mesmo departamento cujo nome corresponde ao termo, excluindo IDs já vinculados
$stmt = $conn->prepare("
    SELECT id, nome AS text 
    FROM usuarios 
    WHERE tipo = 'Estudante' 
      AND departamento_id = ? 
      AND nome LIKE ? 
      $excluir_sql
    ORDER BY nome ASC
    LIMIT 20
");

$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);

